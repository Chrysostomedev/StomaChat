{{-- resources/views/chatbot.blade.php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant StomaCp</title>

    {{-- Bootstrap 5 CSS --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet"> --}} --}}

    {{-- <style>
        body {
            background-color: #f8f9fa;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode body {
            background-color: #1e1e2f;
            color: #f1f1f1;
        }

        #chatContainer {
            max-width: 700px;
            height: 500px;
            margin: 50px auto;
            display: flex;
            flex-direction: column;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        #messages {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            background-color: #ffffff;
            transition: background-color 0.3s;
        }
        .dark-mode #messages {
            background-color: #2c2c3c;
        }

        .message-bubble {
            max-width: 75%;
            padding: 0.6rem 1rem;
            border-radius: 15px;
            margin-bottom: 0.5rem;
            word-wrap: break-word;
            transition: all 0.3s;
        }
        .message-user {
            background-color: #5c6ac4;
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }
        .message-bot {
            background-color: #f1f1f1;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }
        .dark-mode .message-bot {
            background-color: #2c2c3c;
            color: #e0e0e0;
        }
        .dark-mode .message-user {
            background-color: #7c83f2;
            color: #fff;
        }

        #chatForm {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-top: 1px solid #dee2e6;
            background-color: #ffffff;
            transition: background-color 0.3s;
        }
        .dark-mode #chatForm {
            background-color: #1e1e2f;
            border-color: #444;
        }

        #userMessage {
            flex-grow: 1;
            border: none;
            border-radius: 50px;
            padding: 0.6rem 1rem;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
        }
        #userMessage:focus {
            outline: none;
            box-shadow: 0 0 0 2px #5c6ac4;
        }

        .btn-indigo {
            background-color: #5c6ac4;
            color: #fff;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-indigo:hover {
            background-color: #4e5ab4;
            color: #fff;
        }
    </style> --}}
{{-- </head>
<body> --}}

{{-- <div id="chatContainer">
    <div id="messages">
        {{-- Messages dynamiques
    </div>

    <form id="chatForm">
        <input type="text" id="userMessage" placeholder="Tapez votre message..." autocomplete="off">
        <button type="submit" class="btn btn-indigo"><i class="bi bi-send-fill"></i></button>
    </form>
</div> --}}

{{-- Bootstrap JS --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}} --}}

{{-- <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chatForm');
    const input = document.getElementById('userMessage');
    const messagesDiv = document.getElementById('messages');

    function appendMessage(content, sender = 'bot') {
        const bubble = document.createElement('div');
        bubble.classList.add('message-bubble', sender === 'user' ? 'message-user' : 'message-bot');
        bubble.textContent = content;
        messagesDiv.appendChild(bubble);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        appendMessage(message, 'user');
        input.value = '';
        input.focus();

        fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                appendMessage(data.message, 'bot');
            } else {
                appendMessage("Erreur 😅", 'bot');
            }
        })
        .catch(err => {
            appendMessage("Erreur serveur 😅", 'bot');
            console.error(err);
        });
    });
});
</script> --}}

</body>
</html> --}}
