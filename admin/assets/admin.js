const toggle = document.getElementById("darkMode");

toggle.addEventListener("change",()=>{
    document.body.classList.toggle("dark");
});
