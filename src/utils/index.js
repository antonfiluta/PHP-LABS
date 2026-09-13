const bg = document.createElement('div');
bg.innerHTML = `
        <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-300 to-stone-200 dark:from-stone-950 dark:to-stone-900">
          <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-sky-500/70 blur-3xl dark:bg-sky-200/30"></div>
          <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-sky-500/70 blur-3xl dark:bg-sky-200/20"></div>
        </div>`;
document.body.append(bg);

{
  /* <script src="./utils/index.js"></script> */
}
