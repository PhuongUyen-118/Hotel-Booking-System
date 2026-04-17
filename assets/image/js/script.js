// Gợi ý địa điểm
    const locationInput = document.getElementById("locationInput");
    const suggestions = document.getElementById("suggestions");

    locationInput.addEventListener("focus", () => {
        suggestions.style.display = "block";
    });

    document.addEventListener("click", (e) => {
        if (!locationInput.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = "none";
        }
    });

    document.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
            locationInput.value = item.innerText.split('\n')[0].trim();
            suggestions.style.display = "none";
        });
    });

    // Khách / phòng
    const guestsInput = document.getElementById("guestsInput");
    const guestDropdown = document.getElementById("guestDropdown");

    guestsInput.addEventListener("click", () => {
        guestDropdown.style.display = "block";
    });

    function closeGuestDropdown() {
        guestDropdown.style.display = "none";
    }

    const guestCounts = {
        adults: 1,
        children: 0,
        rooms: 1
    };

    function adjustGuest(type, change) {
        guestCounts[type] += change;
        if (guestCounts[type] < 0) guestCounts[type] = 0;

        document.getElementById(type + "Count").innerText = guestCounts[type];
        guestsInput.value = `${guestCounts.adults} adult · ${guestCounts.children} children · ${guestCounts.rooms} room`;
    }
