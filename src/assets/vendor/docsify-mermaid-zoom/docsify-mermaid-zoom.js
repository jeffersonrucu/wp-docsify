const M = `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <circle cx="12" cy="12" r="3"></circle>
        <path d="M17 17l-2.5 -2.5"></path>
        <path d="M10 5l2 -2l2 2"></path>
        <path d="M19 10l2 2l-2 2"></path>
        <path d="M5 10l-2 2l2 2"></path>
        <path d="M10 19l2 2l2 -2"></path>
    </svg>
`,
  C = `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M21 21l-6 -6"></path>
        <path d="M3.268 12.043a7.017 7.017 0 0 0 6.634 4.957a7.012 7.012 0 0 0 7.043 -6.131a7 7 0 0 0 -5.314 -7.672a7.021 7.021 0 0 0 -8.241 4.403"></path>
        <path d="M3 4v4h4"></path>
    </svg>
`,
  E =
    (p = 1, u = 5, v = !0) =>
    (g) => {
      const o = document.querySelector(`#${g}`),
        r = o.parentNode,
        i = d3.select(o);
      i.html("<g>" + i.html() + "</g>");
      const l = i.select("g"),
        s = d3.zoom();
      o.addEventListener("click", () => d());
      const d = () => {
          s
            .on("zoom", (e) => l.attr("transform", e.transform))
            .scaleExtent([p, u]),
            i.call(s);
        },
        a = () => {
          i.on(".zoom", null);
        },
        c = () => {
          l.transition().call(s.scaleTo, 1),
            l
              .transition()
              .call(s.translateTo, 0.5 * o.offsetWidth, 0.5 * o.offsetHeight),
            a();
        },
        w = (e) => {
          const t = e.getBoundingClientRect();
          return (
            t.top >= 0 &&
            t.left >= 0 &&
            t.bottom <=
              (window.innerHeight || document.documentElement.clientHeight) &&
            t.right <=
              (window.innerWidth || document.documentElement.clientWidth)
          );
        };
      document.addEventListener(
        "scroll",
        () => {
          w(r) || (a(), c());
        },
        {
          passive: !0,
        }
      ),
        v &&
          ((e) => {
            const t = document.createElement("div");
            (t.style.visibility = "hidden"),
              e.addEventListener("mouseover", () => {
                (t.style.visibility = "visible"),
                  (t.style.display = "flex"),
                  (t.style.justifyContent = "flex-start");
              }),
              e.addEventListener("mouseleave", () => {
                t.style.visibility = "hidden";
              });
            const h = (b, k) => {
                const n = document.createElement("button");
                return (
                  (n.innerHTML = k),
                  (n.style.backgroundColor = "transparent"),
                  (n.style.border = "none"),
                  (n.style.cursor = "pointer"),
                  n.addEventListener("click", () => b()),
                  n
                );
              },
              f = h(d, M),
              y = h(c, C);
            t.appendChild(f), t.appendChild(y), e.appendChild(t);
          })(r);
    },
  { mermaidZoom: Z = {}, mermaidConfig: m = {} } = window.$docsify;
m.postRenderCallback = E(...Object.values(Z));
window.$docsify.mermaidConfig = m;
