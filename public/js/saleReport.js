import { closeErrorBox } from './primaryFormOperations.js'

let customerWiseExplore = document.querySelectorAll('.getMoreByCustBtn')
let monthWiseExplore = document.querySelectorAll('.monthWiseExploreBtn')

monthWiseExplore.forEach(ele => {
    ele.addEventListener('click', e => {
        e.preventDefault()
        let p = ele.parentElement.parentElement.children[2]
        p.classList.toggle('d-none')
        if (p.classList.contains('d-none')) ele.innerHTML = '&hArr;'
        else ele.innerHTML = '&dArr;'
    })
})

customerWiseExplore.forEach(ele => {
    ele.addEventListener('click', e => {
        e.preventDefault()
        let p = ele.parentElement.parentElement.nextElementSibling
        p.classList.toggle('d-none')
        if (p.classList.contains('d-none')) ele.innerHTML = '&hArr;'
        else ele.innerHTML = '&dArr;'
    })
})

let billNumbers = document.querySelectorAll('.bill-number')
let searchNumber = document.getElementById('billNo')
let searchDate = document.getElementById('date')
let searchBtn = document.getElementById('search-button')
let clearBtn = document.getElementById('clear-btn')
searchBtn.addEventListener('click', e => {
    e.preventDefault()
    let f = 0,
        f1 = 0
    if (searchDate.value == '') {
        alert('please! select date for search bill...')
        f = 1
    }

    if (searchNumber.value == '') {
        alert('please! Enter bill number for search bill...')
        f1 = 1
    }

    if (f == 0 && f1 == 0) {
        billNumbers.forEach(number => {
            let date = number.nextElementSibling.textContent
            let billNo = number.textContent

            if (date == searchDate.value && billNo == searchNumber.value) {
                number.parentElement.style.display = ''
            } else {
                number.parentElement.style.display = 'none'
            }
        })
    }
})

clearBtn.addEventListener('click', e => {
    e.preventDefault()
    searchDate.value = ''
    searchNumber.value = ''
    billNumbers.forEach(num => {
        num.parentElement.style.display = ''
    })
})

let fromDate = document.getElementById('fromDate')
let toDate = document.getElementById('toDate')
let searchBtn1 = document.getElementById('search-report')
let clearBtn1 = document.getElementById('clear-btn-1')
let billDate = document.querySelectorAll('.bill-date')
let printBtn = document.querySelector('#printBtn')
searchBtn1.addEventListener('click', e => {
    e.preventDefault()
    let f1 = 0,
        f2 = 0
    if (fromDate.value == '') {
        alert('The from date not should be empty!')
        f1 = 1
    }

    if (toDate.value == '') {
        alert('The To date not should be empty!')
        f2 = 1
    }

    if (f1 == 0 && f2 == 0) {
        billDate.forEach(ele => {
            let p = ele.parentElement
            if (
                ele.textContent >= fromDate.value &&
                ele.textContent <= toDate.value
            ) {
                p.style.display = ''
            } else {
                p.style.display = 'none'
            }
        })

        printBtn.setAttribute(
            'href',
            `http://localhost:8000/reports/sale/print/${fromDate.value.trim()}/${toDate.value.trim()}`
        )
    }
})

clearBtn1.addEventListener('click', e => {
    e.preventDefault()
    fromDate.value = ''
    toDate.value = ''
    billNumbers.forEach(num => {
        num.parentElement.style.display = ''
    })
    printBtn.setAttribute('href', 'http://localhost:8000/reports/sale/print')
})

document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
closeErrorBox()
