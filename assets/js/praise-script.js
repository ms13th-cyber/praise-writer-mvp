window.addEventListener('load', function() {
    if (typeof praise_vars === 'undefined') return;
    const config = praise_vars;

    // 紙吹雪：500文字を超えたら豪華に
    confetti({
        particleCount: parseInt(config.word_count) > 500 ? 250 : 100,
        spread: 80,
        origin: { y: 0.6 },
        zIndex: 9999
    });

    const header = document.querySelector('.wp-header-end');
    if (!header) return;

    const card = document.createElement('div');
    card.className = 'praise-card';
    card.innerHTML = `
        <h3 class="praise-card-title">🎉 ${config.message}</h3>
        <p class="praise-card-stats">分析結果：<strong>${config.word_count}文字</strong>を執筆。筆致は「${config.style_label}」です。</p>
        <a href="${config.share_url}" target="_blank" class="praise-share-btn">達成感をシェアする</a>
    `;
    header.after(card);
});