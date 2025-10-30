document.addEventListener("DOMContentLoaded", () => {
    const hall = document.getElementById("hall-map");

    if (!hall) return;

    // Загружаем ресурсы
    fetch("/admin/hall/resources")
        .then(res => res.json())
        .then(data => {
            hall.innerHTML = ""; // очистка карты
            data.forEach(drawTable);
        })
        .catch(err => console.error("Ошибка загрузки ресурсов:", err));

    // Кнопки "добавить на карту"
    document.querySelectorAll(".add-to-map").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            fetch(`/admin/hall/add/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrf(),
                },
            })
                .then(res => {
                    if (!res.ok) throw new Error("Ошибка добавления");
                    return res.json();
                })
                .then(drawTable)
                .catch(err => alert(err.message));
        });
    });

    // Отрисовка стола на карте
    function drawTable(r) {
        if (r.x === null || r.y === null) return;

        const div = document.createElement("div");
        div.className = "absolute bg-green-500 text-white text-center rounded cursor-move flex items-center justify-center";
        div.style.width = (r.width || 100) + "px";
        div.style.height = (r.height || 60) + "px";
        div.style.left = r.x + "px";
        div.style.top = r.y + "px";
        div.style.userSelect = "none";
        div.innerText = r.code || r.model?.name || `Стол ${r.id}`;

        // === Перетаскивание ===
        let offsetX, offsetY;
        div.addEventListener("mousedown", (e) => {
            e.preventDefault();
            offsetX = e.offsetX;
            offsetY = e.offsetY;
            document.onmousemove = (ev) => {
                const x = ev.pageX - hall.offsetLeft - offsetX;
                const y = ev.pageY - hall.offsetTop - offsetY;
                div.style.left = `${x}px`;
                div.style.top = `${y}px`;
            };
            document.onmouseup = (ev) => {
                document.onmousemove = null;
                document.onmouseup = null;
                const x = parseInt(div.style.left);
                const y = parseInt(div.style.top);
                savePosition(r.id, x, y);
            };
        });

        // === Удаление ===
        div.addEventListener("contextmenu", (e) => {
            e.preventDefault();
            if (confirm("Удалить этот стол с карты?")) {
                removeTable(r.id, div);
            }
        });

        hall.appendChild(div);
    }

    // Сохранение позиции на сервере
    function savePosition(id, x, y) {
        fetch(`/admin/hall/update-position/${id}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrf(),
            },
            body: JSON.stringify({ x, y }),
        }).catch(err => console.error("Ошибка сохранения позиции:", err));
    }

    // Удаление стола с карты
    function removeTable(id, div) {
        fetch(`/admin/hall/remove/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": getCsrf(),
            },
        })
            .then(res => res.json())
            .then(() => div.remove())
            .catch(err => console.error("Ошибка удаления:", err));
    }

    // Получение CSRF-токена
    function getCsrf() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }
});
