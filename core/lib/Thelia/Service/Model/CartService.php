<?php

namespace Thelia\Service\Model;

use Propel\Runtime\Exception\PropelException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Model\AddressQuery;
use Thelia\Model\Cart;

readonly class CartService
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private TranslatorInterface $translator,
        private ContainerInterface $container,
        private RequestStack $requestStack,
    )
    {
    }

    public function addItem(Form $form,bool $validatedForm = false): void
    {
        $eventDispatcher = $this->eventDispatcher;

        $message = null;

        try {
            if ($validatedForm && !$form->isValid()){
                throw new \RuntimeException('Failed to validate form');
            }

            $cartEvent = $this->getCartEvent();

            $cartEvent->bindForm($form);

            $eventDispatcher->dispatch($cartEvent, TheliaEvents::CART_ADDITEM);

            $this->afterModifyCart($eventDispatcher);

        } catch (PropelException $e) {
            Tlog::getInstance()->error(sprintf('Failed to add item to cart with message : %s', $e->getMessage()));
            $message = $this->translator->trans(
                'Failed to add this article to your cart, please try again'
            );
        } catch (FormValidationException $e) {
            $message = $e->getMessage();
        }

        if ($message) {
            throw new \RuntimeException($message);
        }
    }

    public function deleteItem(int $cartItemId): void
    {
        $eventDispatcher = $this->eventDispatcher;
        $cartEvent = $this->getCartEvent();
        $cartEvent->setCartItemId($cartItemId);

        try {
            $eventDispatcher->dispatch($cartEvent, TheliaEvents::CART_DELETEITEM);
            $this->afterModifyCart($eventDispatcher);
        } catch (\Exception $e) {
            Tlog::getInstance()->error(sprintf('error during deleting cartItem with message : %s', $e->getMessage()));
            throw new \RuntimeException('Failed to delete cartItem');
        }
    }

    public function changeItem(int $cartItemId,int $quantity): void
    {
        $eventDispatcher = $this->eventDispatcher;
        $cartEvent = $this->getCartEvent();
        $cartEvent->setCartItemId($cartItemId);
        $cartEvent->setQuantity($quantity);

        try {
            $eventDispatcher->dispatch($cartEvent, TheliaEvents::CART_UPDATEITEM);
            $this->afterModifyCart($eventDispatcher);
        } catch (\Exception $e) {
            Tlog::getInstance()->error(sprintf('Failed to change cart item quantity: %s', $e->getMessage()));
            throw new \RuntimeException('Failed to change cart item quantity');
        }
    }


    public function getCart(): Cart
    {
        return $this->requestStack->getCurrentRequest()?->getSession()->getSessionCart($this->eventDispatcher);
    }

    public function clearCart(): void
    {
        $this->requestStack->getCurrentRequest()?->getSession()->clearSessionCart($this->eventDispatcher);
    }

    /**
     * @throws PropelException
     */
    protected function afterModifyCart(EventDispatcherInterface $eventDispatcher): void
    {
        /* recalculate postage amount */
        $session = $this->requestStack->getCurrentRequest()?->getSession();
        $order = $session->getOrder();
        if (null !== $order) {
            $deliveryModule = $order->getModuleRelatedByDeliveryModuleId();
            $deliveryAddress = AddressQuery::create()->findPk($order->getChoosenDeliveryAddress());

            if (null !== $deliveryModule && null !== $deliveryAddress) {
                $moduleInstance = $deliveryModule->getDeliveryModuleInstance($this->container);

                $orderEvent = new OrderEvent($order);

                try {
                    $deliveryPostageEvent = new DeliveryPostageEvent(
                        $moduleInstance,
                        $session->getSessionCart($eventDispatcher),
                        $deliveryAddress
                    );

                    $eventDispatcher->dispatch(
                        $deliveryPostageEvent,
                        TheliaEvents::MODULE_DELIVERY_GET_POSTAGE,
                    );

                    $postage = $deliveryPostageEvent->getPostage();

                    if (null !== $postage) {
                        $orderEvent->setPostage($postage->getAmount());
                        $orderEvent->setPostageTax($postage->getAmountTax());
                        $orderEvent->setPostageTaxRuleTitle($postage->getTaxRuleTitle());
                    }

                    $eventDispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_POSTAGE);
                } catch (\Exception $ex) {
                    // The postage has been chosen, but changes in the cart causes an exception.
                    // Reset the postage data in the order
                    $orderEvent->setDeliveryModule(0);

                    $eventDispatcher->dispatch($orderEvent, TheliaEvents::ORDER_SET_DELIVERY_MODULE);
                }
            }
        }
    }

    protected function getCartEvent(): CartEvent
    {
        $cart = $this->requestStack->getCurrentRequest()?->getSession()->getSessionCart($this->eventDispatcher);

        return new CartEvent($cart);
    }
}
