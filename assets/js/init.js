window.onload = loadScript;

function loadScript() {
  if(!navigator.onLine) {
    initialize();
  }
}