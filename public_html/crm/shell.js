const TAB_IDS = ['sverka', 'smeny', 'odr', 'marketing', 'tasks'];
const tabs = document.querySelectorAll('.crm-tab');
const frames = Object.fromEntries(
  TAB_IDS.map((id) => [id, document.getElementById(`frame-${id}`)]).filter(([, f]) => f)
);

tabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    const id = tab.dataset.tab;
    if (!id || !frames[id]) return;

    tabs.forEach((t) => {
      const active = t === tab;
      t.classList.toggle('active', active);
      t.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    Object.entries(frames).forEach(([key, frame]) => {
      const active = key === id;
      frame.classList.toggle('active', active);
      frame.hidden = !active;
    });

    try {
      history.replaceState(null, '', id === 'sverka' ? './' : `./?tab=${id}`);
    } catch (_) { /* ignore */ }
  });
});

const params = new URLSearchParams(location.search);
const initial = params.get('tab');
if (initial && frames[initial]) {
  document.querySelector(`.crm-tab[data-tab="${initial}"]`)?.click();
}
