const pc = document.getElementById('particles');
['#6c63ff','#ff6584','#43e8a0','#ffaa6a','#6ac8ff'].forEach((color,i) => {
    for (let j=0;j<4;j++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random()*4+2;
        p.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;background:${color};box-shadow:0 0 ${size*2}px ${color};animation-duration:${Math.random()*20+15}s;animation-delay:${Math.random()*20}s`;
        pc.appendChild(p);
    }
});
