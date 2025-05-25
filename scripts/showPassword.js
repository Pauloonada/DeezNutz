const btn = document.getElementById("reveal-btn");
const pwText = document.getElementById("password-text");

btn.addEventListener("click", () => {
    pwText.textContent = 'You lil snitch 😏';
    btn.disabled = true;
    btn.textContent = "Too Late!";
});