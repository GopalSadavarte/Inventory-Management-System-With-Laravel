import { reportOp } from './reportPrimary.js'
reportOp()

let searchId = document.getElementById('p-id')
let searchName = document.getElementById('product-name')
let pInfoTable = document.querySelectorAll('.product-info-table')
let msg = document.getElementById('notFoundText')
searchId.addEventListener('input', e => {
    e.preventDefault()
    let idV = searchId.value.trim().toUpperCase()
    let count1 = 0
    display()
    pInfoTable.forEach(table => {
        let count = 0
        let pId = table.querySelectorAll('.product-id')
        pId.forEach(id => {
            if (id.textContent.toUpperCase().indexOf(idV) > -1) {
                count++
                id.parentElement.classList.remove('d-none')
            } else {
                id.parentElement.classList.add('d-none')
            }
        })
        if (count == 0) {
            table.parentElement.classList.add('d-none')
            table.parentElement.previousElementSibling.previousElementSibling.classList.add(
                'd-none'
            )
            table.parentElement.previousElementSibling.classList.add('d-none')
        } else {
            count1++
        }
    })
    if (count1 == 0) {
        msg.parentElement.classList.remove('d-none')
    } else {
        msg.parentElement.classList.add('d-none')
    }
})

searchName.addEventListener('input', e => {
    e.preventDefault()
    let pNameV = searchName.value.trim().toUpperCase()
    let count1 = 0
    display()
    pInfoTable.forEach(table => {
        let pName = table.querySelectorAll('.product-name')
        let count = 0
        pName.forEach(name => {
            if (name.textContent.toUpperCase().indexOf(pNameV) > -1) {
                count++
                name.parentElement.classList.remove('d-none')
            } else {
                name.parentElement.classList.add('d-none')
            }
        })
        if (count == 0) {
            table.parentElement.classList.add('d-none')
            table.parentElement.previousElementSibling.classList.add('d-none')
            table.parentElement.previousElementSibling.previousElementSibling.classList.add(
                'd-none'
            )
        } else {
            count1++
        }
    })
    if (count1 == 0) {
        msg.parentElement.classList.remove('d-none')
    } else {
        msg.parentElement.classList.add('d-none')
    }
})

function display () {
    pInfoTable.forEach(table => {
        table.parentElement.classList.remove('d-none')
        table.parentElement.previousElementSibling.classList.remove('d-none')
        table.parentElement.previousElementSibling.previousElementSibling.classList.remove(
            'd-none'
        )
        table.querySelectorAll('.product-id').forEach(id => {
            id.parentElement.classList.remove('d-none')
        })
    })
}
