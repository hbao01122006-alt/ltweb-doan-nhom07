/*
 * canhan.js - Hoàng Gia Bảo
 * Tương tác trên trang cá nhân:
 * 1. Phượng Hoàng với các nút B - A - O.
 * 2. Todo List lưu bằng localStorage.
 * 3. Mini-game Gấu Trúc Hứng FastFood.
 * Thử bằng chuột và bàn phím trên màn hình 360px.
 */

document.addEventListener("DOMContentLoaded", () => {

  /* ========================================================
     1. PHƯỢNG HOÀNG LỬA B - A - O
  ======================================================== */

  const phoenixAvatar = document.getElementById("phoenix-avatar");
  const phoenixSpeech = document.getElementById("phoenix-speech");
  const flames = document.querySelectorAll(".flicker-flame");

  const btnB = document.getElementById("btn-p-b");
  const btnA = document.getElementById("btn-p-a");
  const btnO = document.getElementById("btn-p-o");

  let state = "normal";

  function speak(text, duration = 4000) {
    if (!phoenixSpeech) return;

    phoenixSpeech.textContent = text;
    phoenixSpeech.style.display = "block";

    setTimeout(() => {
      phoenixSpeech.style.display = "none";
    }, duration);
  }

  function triggerB() {
    if (!phoenixAvatar || state === "ash") return;

    speak("🔥 Phượng hoàng bùng cháy dữ dội...");

    setTimeout(() => {
      phoenixAvatar.textContent = "💨";
      phoenixAvatar.style.filter = "grayscale(100%)";

      speak(
        "💨 Phượng hoàng đã thiêu rụi thành đống tro tàn..."
      );

      state = "ash";
    }, 800);
  }

  function triggerA() {
    if (!phoenixAvatar) return;

    if (state === "normal") {
      speak("✨ Phượng hoàng 🐦‍🔥 vẫn đang rực rỡ sức sống!");
      return;
    }

    speak(
      "🔥 Từ trong tro tàn, ngọn lửa Niết bàn bùng cháy!"
    );

    flames.forEach((flame) => {
      flame.classList.add("active");
    });

    setTimeout(() => {
      phoenixAvatar.textContent = "🐦‍🔥";
      phoenixAvatar.style.filter =
        "drop-shadow(0 0 12px #ffc928)";

      state = "normal";

      speak(
        "🔥 PHƯỢNG HOÀNG NIẾT BÀN!\n" +
        "Chúc Gia Bảo bứt phá mọi giới hạn học tập, " +
        "tái sinh mạnh mẽ qua từng mùa thi! 🎓✨",
        6000
      );

      setTimeout(() => {
        flames.forEach((flame) => {
          flame.classList.remove("active");
        });
      }, 5000);

    }, 1000);
  }

  function triggerO() {
    if (!phoenixAvatar) return;

    if (state === "ash") {
      speak(
        "💨 Đang là tro tàn, hãy bấm A để Niết bàn tái sinh trước!"
      );
      return;
    }

    speak("🦅 Phượng hoàng thu mình bay về góc...");

    phoenixAvatar.style.transform =
      "translateY(-20px) rotate(-360deg) scale(0.3)";

    setTimeout(() => {
      phoenixAvatar.textContent = "🥚";
      phoenixAvatar.style.transform = "none";
      phoenixAvatar.style.filter =
        "drop-shadow(0 0 8px #ffc928)";

      speak(
        "🥚 Phượng hoàng đã biến thành quả trứng thần kỳ!"
      );

      state = "egg";
    }, 800);
  }

  if (btnB) {
    btnB.addEventListener("click", triggerB);
  }

  if (btnA) {
    btnA.addEventListener("click", triggerA);
  }

  if (btnO) {
    btnO.addEventListener("click", triggerO);
  }

  document.addEventListener("keydown", (event) => {
    const activeElement = document.activeElement;

    if (
      activeElement &&
      (
        activeElement.tagName === "INPUT" ||
        activeElement.tagName === "TEXTAREA"
      )
    ) {
      return;
    }

    const key = event.key.toLowerCase();

    if (key === "b") {
      triggerB();
    } else if (key === "a") {
      triggerA();
    } else if (key === "o") {
      triggerO();
    }
  });


  /* ========================================================
     2. TODO LIST HỌC TẬP
  ======================================================== */

  const formTodo = document.getElementById("form-todo");
  const inputTodo = document.getElementById("input-todo");
  const listTodo = document.getElementById("danh-sach-todo");

  let todos = [];

  try {
    const duLieuDaLuu =
      JSON.parse(localStorage.getItem("gb_todos"));

    if (Array.isArray(duLieuDaLuu)) {
      todos = duLieuDaLuu;
    } else {
      todos = [
        {
          text: "Hoàn thiện dự án Foodspead",
          done: false
        },
        {
          text: "Ôn tập Thiết kế và Lập trình Web",
          done: true
        }
      ];
    }
  } catch (error) {
    console.error("Không đọc được Todo từ localStorage:", error);

    todos = [
      {
        text: "Hoàn thiện dự án Foodspead",
        done: false
      },
      {
        text: "Ôn tập Thiết kế và Lập trình Web",
        done: true
      }
    ];
  }

  function saveTodos() {
    localStorage.setItem(
      "gb_todos",
      JSON.stringify(todos)
    );
  }

  function renderTodos() {
    if (!listTodo) return;

    listTodo.textContent = "";

    todos.forEach((item, index) => {

      const li = document.createElement("li");

      if (item.done) {
        li.classList.add("completed");
      }

      /* Nội dung Todo */
      const span = document.createElement("span");

      span.textContent = item.text;
      span.classList.add("todo-text");

      span.setAttribute("tabindex", "0");
      span.setAttribute("role", "button");

      span.setAttribute(
        "aria-label",
        item.done
          ? `Đánh dấu chưa hoàn thành: ${item.text}`
          : `Đánh dấu hoàn thành: ${item.text}`
      );

      function toggleTodo() {
        todos[index].done = !todos[index].done;
        saveTodos();
        renderTodos();
      }

      span.addEventListener("click", toggleTodo);

      span.addEventListener("keydown", (event) => {
        if (
          event.key === "Enter" ||
          event.key === " "
        ) {
          event.preventDefault();
          toggleTodo();
        }
      });

      /* Nút xóa */
      const deleteButton =
        document.createElement("button");

      deleteButton.type = "button";
      deleteButton.className = "btn-del-todo";
      deleteButton.textContent = "Xóa";

      deleteButton.setAttribute(
        "aria-label",
        `Xóa công việc: ${item.text}`
      );

      deleteButton.addEventListener("click", () => {
        todos.splice(index, 1);
        saveTodos();
        renderTodos();
      });

      li.appendChild(span);
      li.appendChild(deleteButton);

      listTodo.appendChild(li);
    });

    saveTodos();
  }

  if (formTodo && inputTodo) {
    formTodo.addEventListener("submit", (event) => {
      event.preventDefault();

      const value = inputTodo.value.trim();

      if (value === "") {
        return;
      }

      todos.push({
        text: value,
        done: false
      });

      inputTodo.value = "";

      saveTodos();
      renderTodos();

      inputTodo.focus();
    });
  }

  renderTodos();


  /* ========================================================
     3. MINI-GAME GẤU TRÚC HỨNG FASTFOOD
  ======================================================== */

  const panda = document.getElementById("game-panda");
  const burger = document.getElementById("game-burger");
  const scoreVal = document.getElementById("val-score");
  const btnStart = document.getElementById("btn-start-game");

  let score = 0;
  let pandaX = 50;
  let burgerX = 50;
  let burgerY = -40;
  let isPlaying = false;
  let gameInterval = null;

  function updatePandaPosition() {
    if (!panda) return;

    panda.style.left = `${pandaX}%`;
  }

  document.addEventListener("keydown", (event) => {

    if (!isPlaying) return;

    if (
      event.key === "ArrowLeft" &&
      pandaX > 6
    ) {
      event.preventDefault();
      pandaX -= 6;
    }

    if (
      event.key === "ArrowRight" &&
      pandaX < 94
    ) {
      event.preventDefault();
      pandaX += 6;
    }

    updatePandaPosition();
  });

  if (
    btnStart &&
    burger &&
    panda &&
    scoreVal
  ) {

    btnStart.addEventListener("click", () => {

      if (isPlaying) return;

      score = 0;
      pandaX = 50;
      burgerY = 0;

      scoreVal.textContent = String(score);

      updatePandaPosition();

      burgerX =
        Math.floor(Math.random() * 80) + 10;

      burger.style.left = `${burgerX}%`;

      isPlaying = true;

      btnStart.disabled = true;
      btnStart.textContent = "🎮 Đang chơi...";

      gameInterval = setInterval(() => {

        burgerY += 6;

        burger.style.top = `${burgerY}px`;

        if (burgerY >= 180) {

          if (
            Math.abs(burgerX - pandaX) < 14
          ) {
            score += 10;

            scoreVal.textContent =
              String(score);
          }

          burgerY = -30;

          burgerX =
            Math.floor(Math.random() * 80) + 10;

          burger.style.left =
            `${burgerX}%`;
        }

      }, 50);
    });

  }

});