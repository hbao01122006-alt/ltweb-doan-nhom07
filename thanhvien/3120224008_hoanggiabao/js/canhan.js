document.addEventListener("DOMContentLoaded", () => {

  /* ========================================================
     1. PHƯỢNG HOÀNG LỬA B - A - O (NÚT & BÀN PHÍM)
  ======================================================== */
  const phoenixAvatar = document.getElementById("phoenix-avatar");
  const phoenixSpeech = document.getElementById("phoenix-speech");
  const flames = document.querySelectorAll(".flicker-flame");

  const btnB = document.getElementById("btn-p-b");
  const btnA = document.getElementById("btn-p-a");
  const btnO = document.getElementById("btn-p-o");

  let state = "normal"; // "normal", "ash", "egg"

  function speak(text, duration = 4000) {
    phoenixSpeech.innerText = text;
    phoenixSpeech.style.display = "block";
    setTimeout(() => {
      phoenixSpeech.style.display = "none";
    }, duration);
  }

  // Hành động Bốc lửa thành tro (Bấm B)
  function triggerB() {
    if (state === "ash") return;
    speak("🔥 Phượng hoàng bùng cháy dữ dội...");

    setTimeout(() => {
      phoenixAvatar.innerText = "💨";
      phoenixAvatar.style.filter = "grayscale(100%)";
      speak("💨 Phượng hoàng đã thiêu rụi thành đống tro tàn...");
      state = "ash";
    }, 800);
  }

  // Hành động Niết bàn tái sinh (Bấm A)
  function triggerA() {
    if (state === "normal") {
      speak("✨ Phượng hoàng 🐦‍🔥 vẫn đang rực rỡ sức sống!");
      return;
    }

    speak("🔥 Từ trong tro tàn, ngọn lửa Niết bàn bùng cháy!");
    flames.forEach(f => f.classList.add("active"));

    setTimeout(() => {
      phoenixAvatar.innerText = "🐦‍🔥";
      phoenixAvatar.style.filter = "drop-shadow(0 0 12px #ffc928)";
      state = "normal";

      speak("🔥 PHƯỢNG HOÀNG NIẾT BÀN!\nChúc Gia Bảo bứt phá mọi giới hạn học tập, tái sinh mạnh mẽ qua từng mùa thi! 🎓✨", 6000);

      setTimeout(() => {
        flames.forEach(f => f.classList.remove("active"));
      }, 5000);
    }, 1000);
  }

  // Hành động Bay về góc đẻ trứng (Bấm O)
  function triggerO() {
    if (state === "ash") {
      speak("💨 Đang là tro tàn, hãy bấm 'A' để Niết bàn tái sinh trước!");
      return;
    }

    speak("🦅 Phượng hoàng thu mình bay về góc...");
    phoenixAvatar.style.transform = "translateY(-20px) rotate(-360deg) scale(0.3)";

    setTimeout(() => {
      phoenixAvatar.innerText = "🥚";
      phoenixAvatar.style.transform = "none";
      phoenixAvatar.style.filter = "drop-shadow(0 0 8px #ffc928)";
      speak("🥚 Phượng hoàng đã biến thành quả trứng thần kỳ!");
      state = "egg";
    }, 800);
  }

  // Gán sự kiện Click nút
  btnB.addEventListener("click", triggerB);
  btnA.addEventListener("click", triggerA);
  btnO.addEventListener("click", triggerO);

  // Gán sự kiện Lắng nghe Bàn Phím (B, A, O)
  document.addEventListener("keydown", (e) => {
    // Không nhận phím khi đang nhập trong ô To-do list
    if (document.activeElement.tagName === "INPUT" || document.activeElement.tagName === "TEXTAREA") {
      return;
    }

    const key = e.key.toLowerCase();
    if (key === "b") triggerB();
    else if (key === "a") triggerA();
    else if (key === "o") triggerO();
  });


  /* ========================================================
     2. TODO LIST HỌC TẬP
  ======================================================== */
  const formTodo = document.getElementById("form-todo");
  const inputTodo = document.getElementById("input-todo");
  const listTodo = document.getElementById("danh-sach-todo");

  let todos = JSON.parse(localStorage.getItem("gb_todos")) || [
    { text: "Hoàn thiện dự án Foodspead", done: false },
    { text: "Ôn tập Thiết kế và Lập trình Web", done: true }
  ];

  function renderTodos() {
    listTodo.innerHTML = "";
    todos.forEach((item, index) => {
      const li = document.createElement("li");
      if (item.done) li.classList.add("completed");

      li.innerHTML = `
        <span style="cursor:pointer;" onclick="toggleTodo(${index})">${item.text}</span>
        <button class="btn-del-todo" onclick="deleteTodo(${index})">Xóa</button>
      `;
      listTodo.appendChild(li);
    });
    localStorage.setItem("gb_todos", JSON.stringify(todos));
  }

  window.toggleTodo = (index) => {
    todos[index].done = !todos[index].done;
    renderTodos();
  };

  window.deleteTodo = (index) => {
    todos.splice(index, 1);
    renderTodos();
  };

  formTodo.addEventListener("submit", (e) => {
    e.preventDefault();
    const val = inputTodo.value.trim();
    if (val) {
      todos.push({ text: val, done: false });
      inputTodo.value = "";
      renderTodos();
    }
  });

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

  document.addEventListener("keydown", (e) => {
    if (!isPlaying) return;
    if (e.key === "ArrowLeft" && pandaX > 6) {
      pandaX -= 6;
    } else if (e.key === "ArrowRight" && pandaX < 94) {
      pandaX += 6;
    }
    panda.style.left = pandaX + "%";
  });

  btnStart.addEventListener("click", () => {
    if (isPlaying) return;
    score = 0;
    scoreVal.innerText = score;
    isPlaying = true;
    burgerY = 0;
    burgerX = Math.floor(Math.random() * 80) + 10;
    burger.style.left = burgerX + "%";

    const gameInterval = setInterval(() => {
      burgerY += 6;
      burger.style.top = burgerY + "px";

      if (burgerY >= 180) {
        if (Math.abs(burgerX - pandaX) < 14) {
          score += 10;
          scoreVal.innerText = score;
        }
        burgerY = -30;
        burgerX = Math.floor(Math.random() * 80) + 10;
        burger.style.left = burgerX + "%";
      }
    }, 50);
  });
});