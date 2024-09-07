

// WhatsApp chat box
var whatsappButton = document.getElementById('whatsappButton');
var whatsappChatBox = document.getElementById('whatsappChatBox');

if (whatsappButton && whatsappChatBox) {
whatsappButton.onclick = function() {
    whatsappChatBox.style.display = whatsappChatBox.style.display === 'block' ? 'none' : 'block';
    whatsappChatBox.classList.toggle('visible'); // Certifique-se de que a classe é adicionada/alternada
    toggleIcon();
};

function toggleIcon() {
    // Verifica se a classe 'visible' está presente e altera o ícone de acordo
    if (whatsappChatBox.classList.contains('visible')) {
    whatsappButton.innerHTML = '<i class="fa-solid fa-x"></i>';
    } else {
    whatsappButton.innerHTML = '<i class="fa-brands fa-whatsapp"></i>';
    }
}

// Fechar o chatbox quando clicar no botão de fechar
document.getElementById('closeChatBox')?.addEventListener('click', function() {
    whatsappChatBox.style.display = 'none';
    whatsappChatBox.classList.remove('visible');
    toggleIcon();
});
} else {
console.error('WhatsApp button or chat box not found.');
}