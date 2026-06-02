</main>
<footer class="bg-surface-container-highest dark:bg-inverse-surface border-t border-outline-variant dark:border-outline mt-auto">
    <div class="flex flex-col items-center justify-center w-full py-stack-lg px-gutter text-center max-w-container-max mx-auto">
        <div class="flex gap-8 mb-6">
            <a class="text-on-surface-variant font-body-md hover:text-primary transition-colors" href="#">Confidentialité</a>
            <a class="text-on-surface-variant font-body-md hover:text-primary transition-colors" href="#">Conditions</a>
            <a class="text-on-surface-variant font-body-md hover:text-primary transition-colors" href="#">Support</a>
        </div>
        <div class="font-label-md font-bold text-primary mb-2 uppercase tracking-widest">Mon Magasin</div>
        <p class="text-on-surface-variant font-body-md">© <?= date('Y') ?> Mon Magasin — cours PHP 2026</p>
    </div>
</footer>

<!-- ============================================================
     CHATBOT ASSISTANT — MON MAGASIN
     Visible sur toutes les pages via footer.php
============================================================ -->
<style>
#chatbot-bubble { transition: all 0.3s cubic-bezier(.4,0,.2,1); }
#chatbot-window { transition: all 0.3s cubic-bezier(.4,0,.2,1); transform-origin: bottom right; }
#chatbot-window.hidden { transform: scale(0.85); opacity: 0; pointer-events: none; }
#chatbot-window.visible { transform: scale(1); opacity: 1; pointer-events: all; }
.chat-msg { animation: fadeUp 0.25s ease; }
@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
#chat-messages::-webkit-scrollbar { width: 4px; }
#chat-messages::-webkit-scrollbar-thumb { background: #c2c6d8; border-radius: 99px; }
.typing-dot { animation: blink 1.2s infinite; }
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes blink { 0%,80%,100%{opacity:0.2} 40%{opacity:1} }
</style>

<!-- Bouton flottant -->
<button id="chatbot-bubble"
    onclick="toggleChat()"
    class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-primary text-white rounded-full shadow-xl flex items-center justify-center hover:bg-blue-700 active:scale-95"
    title="Assistant Mon Magasin">
    <span class="material-symbols-outlined" id="bubble-icon" style="font-size:28px;">chat</span>
</button>

<!-- Fenêtre du chatbot -->
<div id="chatbot-window" class="hidden fixed bottom-24 right-6 z-50 w-80 sm:w-96 bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden" style="max-height:520px;">

    <!-- Header -->
    <div class="bg-primary px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white" style="font-size:20px;">smart_toy</span>
        </div>
        <div class="flex-grow">
            <p class="text-white font-bold text-sm leading-tight">Assistant Mon Magasin</p>
            <p class="text-blue-200 text-xs flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span> En ligne
            </p>
        </div>
        <button onclick="toggleChat()" class="text-white/70 hover:text-white transition p-1">
            <span class="material-symbols-outlined" style="font-size:20px;">close</span>
        </button>
    </div>

    <!-- Messages -->
    <div id="chat-messages" class="flex-grow overflow-y-auto p-4 space-y-3" style="max-height:330px;">
        <!-- Message de bienvenue -->
        <div class="chat-msg flex items-start gap-2">
            <div class="w-7 h-7 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-white" style="font-size:14px;">smart_toy</span>
            </div>
            <div class="bg-gray-100 dark:bg-[#2a2a2a] rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[80%]">
                <p class="text-sm text-gray-800 dark:text-gray-200">Bonjour ! 👋 Je suis votre assistant Mon Magasin. Comment puis-je vous aider ?</p>
            </div>
        </div>
    </div>

    <!-- Suggestions rapides -->
    <div id="quick-replies" class="px-4 pb-2 flex flex-wrap gap-2">
        <button onclick="sendQuick('Quels produits proposez-vous ?')" class="text-xs bg-blue-50 dark:bg-blue-950/40 text-primary border border-primary/20 rounded-full px-3 py-1 hover:bg-primary hover:text-white transition">Nos produits</button>
        <button onclick="sendQuick('Comment passer une commande ?')" class="text-xs bg-blue-50 dark:bg-blue-950/40 text-primary border border-primary/20 rounded-full px-3 py-1 hover:bg-primary hover:text-white transition">Commander</button>
        <button onclick="sendQuick('Quels sont vos codes promo ?')" class="text-xs bg-blue-50 dark:bg-blue-950/40 text-primary border border-primary/20 rounded-full px-3 py-1 hover:bg-primary hover:text-white transition">Codes promo</button>
        <button onclick="sendQuick('Délai de livraison ?')" class="text-xs bg-blue-50 dark:bg-blue-950/40 text-primary border border-primary/20 rounded-full px-3 py-1 hover:bg-primary hover:text-white transition">Livraison</button>
    </div>

    <!-- Input -->
    <div class="border-t border-gray-100 dark:border-gray-800 px-3 py-3 flex gap-2">
        <input id="chat-input" type="text" placeholder="Votre message..."
            class="flex-grow text-sm px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary"
            onkeydown="if(event.key==='Enter') sendMessage()">
        <button onclick="sendMessage()"
            class="w-10 h-10 bg-primary hover:bg-blue-700 rounded-xl flex items-center justify-center text-white transition active:scale-95 flex-shrink-0">
            <span class="material-symbols-outlined" style="font-size:18px;">send</span>
        </button>
    </div>
</div>

<script>
// ---- État du chatbot ----
let chatOpen = false;

function toggleChat() {
    chatOpen = !chatOpen;
    const win  = document.getElementById('chatbot-window');
    const icon = document.getElementById('bubble-icon');
    if (chatOpen) {
        win.classList.remove('hidden');
        setTimeout(() => win.classList.add('visible'), 10);
        icon.textContent = 'close';
        document.getElementById('chat-input').focus();
    } else {
        win.classList.remove('visible');
        setTimeout(() => win.classList.add('hidden'), 300);
        icon.textContent = 'chat';
    }
}

function sendQuick(text) {
    document.getElementById('quick-replies').style.display = 'none';
    addMessage(text, 'user');
    showTyping();
    setTimeout(() => { removeTyping(); addMessage(getReply(text), 'bot'); }, 900);
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const text  = input.value.trim();
    if (!text) return;
    input.value = '';
    document.getElementById('quick-replies').style.display = 'none';
    addMessage(text, 'user');
    showTyping();
    setTimeout(() => { removeTyping(); addMessage(getReply(text), 'bot'); }, 900 + Math.random() * 400);
}

function addMessage(text, who) {
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.className = 'chat-msg flex items-start gap-2 ' + (who === 'user' ? 'flex-row-reverse' : '');
    const avatar = who === 'bot'
        ? `<div class="w-7 h-7 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"><span class="material-symbols-outlined text-white" style="font-size:14px;">smart_toy</span></div>`
        : `<div class="w-7 h-7 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"><span class="material-symbols-outlined text-gray-600 dark:text-gray-300" style="font-size:14px;">person</span></div>`;
    const bubble = who === 'bot'
        ? `<div class="bg-gray-100 dark:bg-[#2a2a2a] rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[80%]"><p class="text-sm text-gray-800 dark:text-gray-200">${text}</p></div>`
        : `<div class="bg-primary rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[80%]"><p class="text-sm text-white">${text}</p></div>`;
    div.innerHTML = avatar + bubble;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function showTyping() {
    const box = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.id = 'typing-indicator';
    div.className = 'chat-msg flex items-start gap-2';
    div.innerHTML = `
        <div class="w-7 h-7 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <span class="material-symbols-outlined text-white" style="font-size:14px;">smart_toy</span>
        </div>
        <div class="bg-gray-100 dark:bg-[#2a2a2a] rounded-2xl rounded-tl-none px-4 py-3 flex gap-1">
            <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
            <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
            <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
        </div>`;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function removeTyping() {
    const el = document.getElementById('typing-indicator');
    if (el) el.remove();
}

// ---- Base de réponses ----
function getReply(msg) {
    const m = msg.toLowerCase();

    if (m.match(/produit|catalogue|vend|article|stock/))
        return "Nous proposons 8 produits premium : MacBook Pro M3, iPhone 15 Pro, Sony WH-1000XM5, Galaxy Watch 6, iPad Air M2, Sonos Era 300, MX Master 3S et Studio Display. Consultez notre <a href='menu.php' class='underline text-primary'>catalogue →</a>";

    if (m.match(/commander|commande|acheter|panier|ajouter/))
        return "Pour commander, cliquez sur un produit dans le catalogue, ajoutez-le au panier, puis rendez-vous dans votre <a href='panier.php' class='underline text-primary'>panier</a> pour finaliser le paiement.";

    if (m.match(/livraison|délai|expédition|recevoir/))
        return "La livraison est estimée à 2–4 jours ouvrés. Elle est <strong>gratuite</strong> pour toute commande supérieure à 500 €, sinon 9,99 €.";

    if (m.match(/promo|coupon|réduction|code|remise/))
        return "Voici quelques codes disponibles : <br>🎟 <strong>BEST30</strong> — 30% de réduction<br>🎟 <strong>WIN20</strong> — 20%<br>🎟 <strong>LAST10</strong> — 10%<br>Saisissez-les dans votre panier !";

    if (m.match(/paiement|payer|carte|paypal|virement/))
        return "Nous acceptons les paiements par <strong>Carte bancaire</strong>, <strong>PayPal</strong> et <strong>Virement bancaire</strong>. Votre paiement est 100% sécurisé 🔒.";

    if (m.match(/garantie|sav|retour|remboursement/))
        return "Tous nos produits sont garantis <strong>1 an minimum</strong>. Une extension de garantie 5 ans est disponible. Pour un retour, contactez-nous via la page <a href='contact.php' class='underline text-primary'>Contact</a>.";

    if (m.match(/compte|connexion|inscription|inscrire|login|register/))
        return "Vous pouvez créer un compte sur <a href='../register.php' class='underline text-primary'>cette page</a> ou vous connecter via <a href='compte.php' class='underline text-primary'>Mon Compte</a>.";

    if (m.match(/contact|email|téléphone|support/))
        return "Vous pouvez nous contacter via le formulaire de <a href='contact.php' class='underline text-primary'>Contact</a>. Notre équipe vous répond sous 24h.";

    if (m.match(/bonjour|salut|bonsoir|hello|hi/))
        return "Bonjour ! 😊 Je suis là pour vous aider. Posez-moi vos questions sur nos produits, livraisons ou paiements !";

    if (m.match(/merci|thanks|super|parfait|nickel|ok/))
        return "Avec plaisir ! 😊 N'hésitez pas si vous avez d'autres questions.";

    if (m.match(/prix|combien|tarif|coût/))
        return "Nos prix varient de <strong>129,99 €</strong> (MX Master 3S) à <strong>1 749 €</strong> (Studio Display). Consultez le <a href='menu.php' class='underline text-primary'>catalogue</a> pour tous les tarifs.";

    return "Je n'ai pas bien compris votre question 🤔. Essayez par exemple : <em>\"livraison\"</em>, <em>\"codes promo\"</em>, <em>\"nos produits\"</em> ou <em>\"paiement\"</em>.";
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
});
</script>
</body>
</html>
