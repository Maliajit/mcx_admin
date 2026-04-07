// Dashboard Real-time Simulation
document.addEventListener('DOMContentLoaded', () => {
    console.log('Dashboard initialized');
    
    const goldPrice = document.querySelector('.gold .price-value');
    const silverPrice = document.querySelector('.silver .price-value');
    
    setInterval(() => {
        if (goldPrice) {
            let current = parseFloat(goldPrice.innerText.replace('₹ ', '').replace(',', ''));
            let change = (Math.random() * 10) - 5;
            goldPrice.innerText = `₹ ${(current + change).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
        
        if (silverPrice) {
            let current = parseFloat(silverPrice.innerText.replace('₹ ', '').replace(',', ''));
            let change = (Math.random() * 20) - 10;
            silverPrice.innerText = `₹ ${(current + change).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
    }, 3000);
});
