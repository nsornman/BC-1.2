const slidingWindow = document.getElementById("box-bg");
const toggleButton = document.getElementById("toggleButton");

if (slidingWindow && toggleButton) {
    let isWindowOpen = false;

    toggleButton.onclick = function() {
        if (isWindowOpen) {
            // Slide the window back down to the bottom and reduce height
            slidingWindow.classList.remove("open"); // Remove the "open" class
        } else {
            // Slide the window up to the middle of the page and increase height
            slidingWindow.classList.add("open"); // Add the "open" class
        }
        
        // Toggle the state
        isWindowOpen = !isWindowOpen;
    }
}