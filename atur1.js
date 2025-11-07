function showTime() {
  const now = new Date();
  const time = now.toLocaleTimeString();
  document.getElementById("clock").textContent = " | " + time;
}
setInterval(showTime, 1000);
showTime();
