document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
document.getElementById('close-btn').addEventListener('click', e => {
    e.preventDefault()
    e.target.parentElement.classList.add('d-none')
})
