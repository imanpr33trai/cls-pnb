document.addEventListener("DOMContentLoaded", function () {
  const micBtn = document.getElementById("startVoiceSearch");
  const inputField = document.getElementById("voiceSearchInput");
  const overlay = document.getElementById("voiceSearchOverlay");
  const spokenText = document.getElementById("spokenText");
  const resultsBox = document.getElementById("voiceSearchResults");

  if (!micBtn || !inputField || !overlay) return;

  if (!("webkitSpeechRecognition" in window)) {
    micBtn.style.display = "none";
    return;
  }

  const recognition = new webkitSpeechRecognition();
  recognition.lang = "en-US";
  recognition.continuous = false;
  recognition.interimResults = false;

  micBtn.addEventListener("click", function () {
    overlay.classList.remove("d-none");
    spokenText.textContent = "Listening...";
    resultsBox.innerHTML = "";
    recognition.start();
  });

  recognition.onresult = function (event) {
    const transcript = event.results[0][0].transcript;
    spokenText.textContent = `You said: "${transcript}"`;
    inputField.value = transcript;

    fetch("partials/voice-search.php?q=" + encodeURIComponent(transcript))
      .then((res) => res.text())
      .then((data) => {
        resultsBox.innerHTML = data;
      });
  };

  recognition.onend = function () {};

  recognition.onerror = function (event) {
    spokenText.textContent = "Error: " + event.error;
  };

  document.addEventListener("click", function (e) {
    if (!overlay.contains(e.target) && e.target !== micBtn) {
      overlay.classList.add("d-none");
    }
  });
});

function restrictToNumbers(selector) {
  $(selector).on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
}
restrictToNumbers("#teluserads");
restrictToNumbers("#adpostalcode");
restrictToNumbers("#adPrice");
restrictToNumbers("#location");
$("#otherCategory").on("input", function () {
  this.value = this.value.replace(/[^a-zA-Z\s\-]/g, "");
});
