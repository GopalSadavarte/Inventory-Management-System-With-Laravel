export function reportOp () {
    let yearly = document.querySelectorAll('.yearlyExploreMoreBtn')
    let productWise = document.querySelectorAll('.exploreMoreProducts')
    let clearBtn = document.getElementById('clear-btn')
    let expDates = document.querySelectorAll('.expiry-dates')

    yearly.forEach(ele => {
        ele.addEventListener('click', e => {
            e.preventDefault()
            let next = ele.parentElement.nextElementSibling.nextElementSibling
            next.classList.toggle('d-none')
            if (next.classList.contains('d-none')) ele.innerHTML = '&hArr;'
            else ele.innerHTML = '&dArr;'
        })
    })

    productWise.forEach(ele => {
        ele.addEventListener('click', e => {
            e.preventDefault()
            let next = ele.parentElement.parentElement.nextElementSibling
            next.classList.toggle('d-none')
            if (next.classList.contains('d-none')) ele.innerHTML = '&hArr;'
            else ele.innerHTML = '&dArr;'
        })
    })

    clearBtn.addEventListener('click', e => {
        e.preventDefault()
        if (fromDate.value != '' && toDate.value != '') {
            fromDate.value = ''
            toDate.value = ''
            expDates.forEach(ele => {
                let p = ele.parentElement
                p.style.display = ''
            })
            displayAll()
        }
    })
}

export function getWeekNumber (date) {
    const d = new Date(
        Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())
    )
    d.setUTCDate(d.getUTCDate() + 3 - ((d.getUTCDay() + 6) % 7))
    const firstThursday = new Date(Date.UTC(d.getUTCFullYear(), 0, 4))
    const weekNumber = Math.ceil(((d - firstThursday) / 86400000 + 1) / 7)
    return weekNumber
}

export function displayAppropriate () {
    let blockProducts = document.querySelectorAll('.block-product-table')
    blockProducts.forEach(product => {
        let inventTable = product.querySelectorAll('.block-inventory-table')
        let count = 0
        inventTable.forEach(invent => {
            let row = invent.querySelectorAll('.inventory-row')
            let count1 = 0
            row.forEach(tr => {
                if (!tr.classList.contains('d-none')) count1++
            })
            if (count1 > 0) {
                invent.parentElement.parentElement.classList.remove('d-none')
                count++
            } else {
                invent.parentElement.parentElement.classList.add('d-none')
            }
        })
        let p = product.parentElement
        let prev = p.previousElementSibling
        let pPrev = prev.previousElementSibling
        if (count > 0) {
            p.classList.remove('d-none')
            prev.classList.remove('d-none')
            pPrev.classList.remove('d-none')
        } else {
            p.classList.add('d-none')
            prev.classList.add('d-none')
            pPrev.classList.add('d-none')
        }
    })
}

export function displayAll () {
    let blockProducts = document.querySelectorAll('.block-product-table')
    blockProducts.forEach(ele => {
        let tr = ele.querySelectorAll('tr')
        tr.forEach(tr => {
            tr.classList.remove('d-none')
            let invent = tr.querySelectorAll('td .block-inventory-table tr')
            invent.forEach(ele => {
                ele.classList.remove('d-none')
            })
        })
        let parentEle = ele.parentElement
        parentEle.classList.remove('d-none')
        parentEle.previousElementSibling.classList.remove('d-none')
        parentEle.previousElementSibling.previousElementSibling.classList.remove(
            'd-none'
        )
    })
}
