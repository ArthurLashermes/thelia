import React from 'react';
import { ReactComponent as IconMinus } from '@icons/minus.svg';
import { ReactComponent as IconPlus } from '@icons/plus.svg';

export default function Quantity({
  mutate,
  quantity,
  max,
  title = false,
  small = false,
  visible = true
}) {
  return (
    <div>
      {title && <span className="mb-3 text-sm text-gray-600">Quantité</span>}
      <div className={`Quantity md:mr-10 ${small ? 'Quantity--small' : null}`}>
        <button
          onClick={() => {
            if (quantity > 1) {
              mutate(quantity - 1);
            }
          }}
          className={`Quantity-btn ${
            quantity === 1 ? 'Quantity-btn--disabled' : ''
          }`}
          tabIndex={visible ? '0' : '-1'}
        >
          <span className="m-auto font-bold text-white">
            <IconMinus className="text-white" />
          </span>
        </button>
        <div className="Quantity-number">
          <span>{quantity}</span>
        </div>
        <button
          onClick={() => {
            if (quantity < max) {
              mutate(quantity + 1);
            }
          }}
          className={`Quantity-btn ${
            quantity === max ? 'Quantity-btn--disabled' : ''
          }`}
          tabIndex={visible ? '0' : '-1'}
        >
          <span className="m-auto font-bold text-white">
            <IconPlus />
          </span>
        </button>
      </div>
    </div>
  );
}
