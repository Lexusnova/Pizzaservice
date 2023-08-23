"use strict";

class Cart {
  constructor() {
    this.cart = [];
  }

  cartLength() {
    return this.cart.length;
  }

  addPizza(pizzaId, pizzaName, pizzaPrice) {
    "use strict";
    this.cart.push([pizzaId, pizzaName, pizzaPrice]);
    let sel = document.getElementById('pizzaselect');
    let opt = document.createElement('option');
    opt.appendChild(document.createTextNode(pizzaName));
    opt.value = pizzaId;
    sel.appendChild(opt);
    console.log(this.cart);

    this.newTotalPrice();
    buttonEnableToggle();
  }

  newTotalPrice() {
    "use strict";
    let price = 0;
    this.cart.forEach((value) => {
      price += value[2];
    });
    let para = document.getElementById('currentPrice');
    para.innerText = price.toFixed(2) + "€";
  }

  deleteAll() {
    "use strict";
    this.cart = [];
    this.price = 0;
    let sel = document.getElementById('pizzaselect');
    while(sel.length != 0) {
      sel.remove(sel.length-1);
    }
    this.newTotalPrice();
    buttonEnableToggle();
  }
  
  deleteSelected() {
    "use strict";
    let sel = document.getElementById('pizzaselect');
    let i = 0;
    while(i < sel.length) {
      if (!sel[i].selected) {
        i++;
        continue;
      }
      sel.remove(i);
      this.cart.splice(i, 1);
    }
    this.newTotalPrice();
  }
}

let shoppingcart = new Cart();

function buttonEnableToggle() {
  "use strict";
  let submitButton = document.getElementById('submit');
  if (formularReady()) submitButton.disabled = false;
  else submitButton.disabled = true;
}

function formularReady() {
  "use strict";
  let textField = document.getElementById('address').value;
  if (!textField) return false;
  if (!shoppingcart.cartLength()) return false;
  return true;
}

function onSubmit() {
  "use strict";
  let select = document.getElementById('pizzaselect');
  for (let i = 0; i < select.length; i++) {
    select[i].selected = true;
  }
}

