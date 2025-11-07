document.addEventListener("DOMContentLoaded", (e) => {
  class Counter extends HTMLElement {
    count = 0;

    constructor() {
      super();
      this.attachShadow({ mode: "open" });
      const div = document.createElement("div");
      const style = document.createElement("style");
      style.innerHTML = `
        button {
            background-color: transparent;
            --color: blueviolet;
            border: 2px solid var(--color);
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            outline: none;
            transition: background-color 100ms ease, color 100ms ease,
            scale 100ms ease, transform 100ms ease, box-shadow 100ms ease;
            cursor: pointer;
            box-shadow: 0 10px 20px var(--color);
        }
        
        button {
            background-color: transparent;
            --color: blueviolet;
            border: 2px solid var(--color);
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            outline: none;
            transition: background-color 100ms ease, color 100ms ease,
            scale 100ms ease, transform 100ms ease, box-shadow 100ms ease;
            cursor: pointer;
            box-shadow: 0 10px 20px var(--color);
        }
        button:hover {
            background-color: var(--color);
            color: white;
            transform: translateY(-4%);
        }
        button:active {
            transform: translateY(4%);
            box-shadow: 0 2px 5px var(--color);
        }
        .counter {
            display: flex;
            padding: 2rem;
            flex-direction: column;
            width: fit-content;
            gap: 1rem;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.5);
            border-radius: 0.5rem;
        }
        .dec {
            --color: red;
        }
        .inc {
            --color: green;
        }
        .count {
            font-size: 1.5rem;
        }
      `;
      div.classList.add("counter");
      div.innerHTML = `
        <span class="count">5</span>
        <div class="controls">
        <button class="dec">Decrease</button>
        <button class="inc">Increase</button>
        </div>
      `;
      this.shadowRoot.appendChild(div);
      this.shadowRoot.appendChild(style);
    }

    connectedCallback() {
      const inc = this.shadowRoot.querySelector(".inc");
      const dec = this.shadowRoot.querySelector(".dec");
      const countEl = this.shadowRoot.querySelector(".count");
      countEl.textContent = this.count;
      inc.addEventListener("click", () => {
        this.count++;
        countEl.textContent = this.count;
      });

      dec.addEventListener("click", () => {
        this.count--;
        countEl.textContent = this.count;
      });

      console.log({ inc, dec });
    }
  }

  window.customElements.define("my-counter", Counter);

  const add = document.querySelector(".add");
  const counters = document.querySelector(".counters");
  add.addEventListener("click", (e) => {
    counters.appendChild(new Counter());
  });
});
