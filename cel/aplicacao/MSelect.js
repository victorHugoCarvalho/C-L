function MSelect(lSelect, rSelect, l2rButton, r2lButton) {
    l2rButton.onclick = opt2Right;
    r2lButton.onclick = opt2Left;

    function opt2Right() {
        for (var i = 0; i < lSelect.length; i++) {
            if (lSelect.options[i].selected) {
                rSelect.options[rSelect.length] = new Option(lSelect.options[i].text, lSelect.options[i].value);
                lSelect.options[i--] = null;
            }
        }
    }

    function opt2Left() {
        for (var i = 0; i < rSelect.length; i++) {
            if (rSelect.options[i].selected) {
                lSelect.options[lSelect.length] = new Option(rSelect.options[i].text, rSelect.options[i].value);
                rSelect.options[i--] = null;
            }
        }
    }
}
