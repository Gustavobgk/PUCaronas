(function () {
    const TIMEOUT_MS = 15 * 60 * 1000; // 15 minutos de inatividade

    let timer;

    function expire() {
        sessionStorage.clear();
        window.location.href = '../usuario/login.html';
    }

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(expire, TIMEOUT_MS);
    }

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (event) {
        document.addEventListener(event, resetTimer, { passive: true });
    });

    resetTimer();
})();
