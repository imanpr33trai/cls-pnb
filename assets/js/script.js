document.addEventListener('DOMContentLoaded', function () {
    const micBtn = document.getElementById('startVoiceSearch');
    const inputField = document.getElementById('voiceSearchInput');
    const overlay = document.getElementById('voiceSearchOverlay');
    const spokenText = document.getElementById('spokenText');
    const resultsBox = document.getElementById('voiceSearchResults');

    if (!micBtn || !inputField || !overlay) return;

    if (!('webkitSpeechRecognition' in window)) {
        micBtn.style.display = "none";
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.continuous = false;
    recognition.interimResults = false;

    micBtn.addEventListener('click', function () {
        overlay.classList.remove('d-none');
        spokenText.textContent = "Listening...";
        resultsBox.innerHTML = "";
        recognition.start();
    });

    recognition.onresult = function (event) {
        const transcript = event.results[0][0].transcript;
        spokenText.textContent = `You said: "${transcript}"`;
        inputField.value = transcript;

        // Fetch search results from server
        fetch('partials/voice-search.php?q=' + encodeURIComponent(transcript))
            .then(res => res.text())
            .then(data => {
                resultsBox.innerHTML = data;
            });
    };

    recognition.onend = function () {
        // recognition stopped
    };

    recognition.onerror = function (event) {
        spokenText.textContent = "Error: " + event.error;
    };

    // Hide overlay on click outside
    document.addEventListener('click', function (e) {
        if (!overlay.contains(e.target) && e.target !== micBtn) {
            overlay.classList.add('d-none');
        }
    });
});





