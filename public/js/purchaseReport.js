import { closeErrorBox } from './primaryFormOperations.js'

let btn = document.querySelectorAll('.table-buttons')
btn.forEach(element => {
    element.addEventListener('click', e => {
        e.preventDefault()
        let ele = element.parentElement.parentElement.nextElementSibling
        ele.classList.toggle('d-none')
        if (ele.classList.contains('d-none')) element.innerHTML = '&hArr;'
        else element.innerHTML = '&dArr;'
    })
})

let fromDate = document.getElementById('search-date1')
let toDate = document.getElementById('search-date2')
let searchBtn = document.getElementById('searchButton')
let purchaseDates = document.querySelectorAll('.purchase-date')
let printBtn = document.getElementById('printButton')
let clearBtn = document.getElementById('clear-btn')
const displayAll = () => {
    purchaseDates.forEach(pDate => {
        pDate.parentElement.style.display = ''
    })
    printBtn.setAttribute('href', 'http://localhost:8000/purchase/print')
}
searchBtn.addEventListener('click', e => {
    e.preventDefault()
    if (fromDate.value != '' && toDate.value != '') {
        purchaseDates.forEach(pDate => {
            let d = pDate.textContent
            if (d >= fromDate.value && d <= toDate.value) {
                pDate.parentElement.style.display = ''
            } else {
                pDate.parentElement.style.display = 'none'
            }
        })

        printBtn.setAttribute(
            'href',
            `http://localhost:8000/purchase/print/${fromDate.value.trim()}/${toDate.value.trim()}`
        )
    } else {
        alert('Please! select dates..')
        displayAll()
    }
})

clearBtn.addEventListener('click', e => {
    e.preventDefault()
    fromDate.value = ''
    toDate.value = ''
    displayAll()
})

document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
closeErrorBox()
