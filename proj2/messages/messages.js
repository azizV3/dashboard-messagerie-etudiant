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
    formData.append("receiver_id", selectedContactId);
    formData.append("message", text);

    try {
        await fetch("send.php", { method: "POST", body: formData });
        input.value = "";
        loadMessages(selectedContactId);
    } catch (e) {
        console.error("Erreur d'envoi:", e);
    }
}

/*
 * Builds one message bubble using DOM nodes + textContent.
 * Nothing coming from the server is ever parsed as HTML, so a message like
 * <img src=x onerror=alert(1)> is displayed as literal text.
 */
function buildMessageElement(msg, isMe) {
    const div = document.createElement("div");
    div.className = `message ${isMe ? "me" : "student"}`;

    const info = document.createElement("div");
    info.className = "message-info";
    info.style.cssText =
        "font-size: 0.75rem; font-weight: bold; margin-bottom: 5px; opacity: 0.8;";
    info.textContent = isMe ? "Moi" : msg.sender_name;

    const text = document.createElement("div");
    text.className = "message-text";
    text.textContent = msg.message_text;

    const time = document.createElement("div");
    time.className = "message-time";
    time.style.cssText =
        "font-size: 0.6rem; margin-top: 5px; text-align: right; opacity: 0.5;";
    // created_at looks like "2026-10-31 14:05:09" -> "14:05"
    const timePart = String(msg.created_at ?? "").split(" ")[1] ?? "";
    time.textContent = timePart.substring(0, 5);

    div.append(info, text, time);
    return div;
}

async function loadMessages(contactId) {
    selectedContactId = contactId;

    document
        .querySelectorAll(".contact-item")
        .forEach(item => item.classList.remove("active"));

    try {
        const response = await fetch(`get.php?contact_id=${encodeURIComponent(contactId)}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        const messagesDiv = document.getElementById("messages");
        messagesDiv.replaceChildren(
            ...data.map(msg => buildMessageElement(msg, msg.sender_id == mySessionId))
        );
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        const header = document.getElementById("chat-header");
        if (header && data.length > 0) {
            header.textContent = "Discussion en cours";
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
