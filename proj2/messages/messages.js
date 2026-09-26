let selectedContactId = null; 

async function envoyerMessage() {
    const input = document.getElementById("message-input");
    const text = input.value.trim();
    
    if (!selectedContactId) {
        alert("Veuillez sélectionner un étudiant pour discuter.");
        return;
    }
    if (!text) return;

    const formData = new FormData();
    formData.append('receiver_id', selectedContactId);
    formData.append('message', text);

    try {
        await fetch('send.php', {
            method: 'POST',
            body: formData
        });
        input.value = "";
        loadMessages(selectedContactId); 
    } catch (e) {
        console.error("Erreur d'envoi:", e);
    }
}

async function loadMessages(contactId) {
    selectedContactId = contactId; 
    
    document.querySelectorAll('.contact-item').forEach(item => item.classList.remove('active'));

    try {
        const response = await fetch(`get.php?contact_id=${contactId}`);
        const data = await response.json();
        
        const messagesDiv = document.getElementById("messages");
        messagesDiv.innerHTML = "";
        
        data.forEach(msg => {
            const isMe = msg.sender_id == mySessionId; 
            const div = document.createElement("div");
            
            div.className = `message ${isMe ? 'me' : 'student'}`;
            div.innerHTML = `
        <div class="message-info" style="font-size: 0.75rem; font-weight: bold; margin-bottom: 5px; opacity: 0.8;">
            ${isMe ? "Moi" : msg.sender_name}
        </div>
        <div class="message-text">
            ${msg.message_text}
        </div>
        <div class="message-time" style="font-size: 0.6rem; margin-top: 5px; text-align: right; opacity: 0.5;">
            ${msg.created_at.split(' ')[1].substring(0, 5)} 
        </div>
    `;
            messagesDiv.appendChild(div);
        });
        
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        
        const header = document.getElementById("chat-header");
        if (header && data.length > 0) {
            header.innerText = "Discussion en cours";
        }
    } catch (error) {
        console.error("Erreur de chargement:", error);
    }
}

setInterval(() => {
    
    if (selectedContactId !== null) {
        loadMessages(selectedContactId);
    }
}, 3000);