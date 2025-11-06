document.addEventListener("DOMContentLoaded", (e) => {
  const intersector = document.querySelector(".intersector");
  const quotesContainer = document.querySelector(".quotes");

  const obs = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting) {
        populate();
      }
    },
    { threshold: 0 }
  );
  obs.observe(intersector);
  let page = 0;
  let perPage = 10;
  const populate = async () => {
    const result = await fetch("./quotes.json");
    const data = await result.json();
    if (page + perPage > data.length) {
      return;
    }
    const pageData = data.slice(page, page + perPage);
    pageData.forEach((d) => {
      const card = document.createElement("div");
      card.classList.add("card");
      card.innerHTML = `
        <blockquote class="quote">
          ${d.text}
        </blockquote>
        <span class="author">${d.author}</span>
        `;
      quotesContainer.appendChild(card);
    });
    page += perPage;
  };
});
