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
let purchaseDateWithoutDealer = document.querySelectorAll('.purchase-date')
let printBtn = document.getElementById('printButton')
let printBtnByDate = document.getElementById('printByDateButton')
let clearBtn = document.getElementById('clear-btn')
let productTables = document.querySelectorAll('.product-info-table')
let entryContainerByDealer = document.querySelector(
    '#entry-container-by-dealer'
)
let otherDealerProductContainer = document.getElementById('other-dealer-report')
let notFoundContainer = document.getElementById('notFoundText')
let printUrl = printBtnByDate.getAttribute('href')

searchBtn.addEventListener('click', e => {
    e.preventDefault()
    displayAllData()
    let count1 = 0
    productTables.forEach(table => {
        let purchaseDates = table.querySelectorAll('.purchase-date-by-dealer')
        let count = 0
        purchaseDates.forEach(date => {
            let d = date.textContent
            if (d >= fromDate.value && d <= toDate.value) {
                count++
                date.parentElement.classList.remove('d-none')
            } else {
                date.parentElement.classList.add('d-none')
            }
        })

        if (count == 0) {
            table.parentElement.parentElement.classList.add('d-none')
            table.parentElement.parentElement.previousElementSibling.classList.add(
                'd-none'
            )
        } else {
            count1++
        }
    })

    if (count1 == 0) {
        entryContainerByDealer.classList.add('d-none')
    }

    let count = 0
    purchaseDateWithoutDealer.forEach(date => {
        let d = date.textContent
        if (d >= fromDate.value && d <= toDate.value) {
            count++
            date.parentElement.classList.remove('d-none')
        } else {
            date.parentElement.classList.add('d-none')
        }
    })

    if (count == 0 && otherDealerProductContainer != null) {
        otherDealerProductContainer.classList.add('d-none')
    }

    if (count == 0 && count1 == 0) {
        notFoundContainer.classList.remove('d-none')
        notFoundContainer.nextElementSibling.classList.add('d-none')
    } else {
        notFoundContainer.classList.add('d-none')
        notFoundContainer.nextElementSibling.classList.remove('d-none')
    }

    printBtnByDate.classList.remove('d-none')
    printBtn.classList.add('d-none')
    let dates = `/${fromDate.value.trim()}/${toDate.value.trim()}`
    let v = printUrl.concat(dates)
    printBtnByDate.setAttribute('href', v)
})

function displayAllData () {
    if (otherDealerProductContainer != null) {
        if (otherDealerProductContainer.classList.contains('d-none')) {
            otherDealerProductContainer.classList.remove('d-none')
        }
    }

    if (entryContainerByDealer.classList.contains('d-none')) {
        entryContainerByDealer.classList.remove('d-none')
    }

    productTables.forEach(table => {
        let purchaseDates = table.querySelectorAll('.purchase-date-by-dealer')
        purchaseDates.forEach(date => {
            if (date.parentElement.classList.contains('d-none')) {
                date.parentElement.classList.remove('d-none')
            }
        })
        table.parentElement.parentElement.classList.remove('d-none')
        table.parentElement.parentElement.previousElementSibling.classList.remove(
            'd-none'
        )
    })

    purchaseDateWithoutDealer.forEach(date => {
        if (date.parentElement.classList.contains('d-none')) {
            date.parentElement.classList.remove('d-none')
        }
    })

    notFoundContainer.classList.add('d-none')
    notFoundContainer.nextElementSibling.classList.remove('d-none')
    printBtn.classList.remove('d-none')
    printBtnByDate.classList.add('d-none')
    printBtnByDate.setAttribute('href', printUrl)
}

clearBtn.addEventListener('click', e => {
    e.preventDefault()
    fromDate.value = ''
    toDate.value = ''
    displayAllData()
})

document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
closeErrorBox()
