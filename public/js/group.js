//add group form script start here
let groupForm = document.getElementById('addGroupForm')
let groupFormBtn = document.querySelectorAll('.group-btn')
let groupInput = document.getElementById('group_name')
let groupId = document.getElementById('groupId')
let method = document.getElementById('method')
groupFormBtn[0].addEventListener('click', e => {
    e.preventDefault()
    window.location.reload()
})
groupFormBtn[1].addEventListener('click', e => {
    e.preventDefault()
    groupFormBtn[0].setAttribute('disabled', true)
    groupFormBtn[2].setAttribute('disabled', true)
    groupId.removeAttribute('readonly')
    if (sessionStorage.key(0) == 'update') sessionStorage.removeItem('update')
    sessionStorage.setItem('delete', 'delete')
    groupId.value = ''
    groupId.focus()
})
groupFormBtn[2].addEventListener('click', e => {
    e.preventDefault()
    groupFormBtn[0].setAttribute('disabled', true)
    groupFormBtn[1].setAttribute('disabled', true)
    groupId.removeAttribute('readonly')
    if (sessionStorage.key(0) == 'delete') sessionStorage.removeItem('delete')
    sessionStorage.setItem('update', 'update')
    groupId.value = ''
    groupId.focus()
})
groupFormBtn[4].addEventListener('click', e => {
    e.preventDefault()
    groupForm.reset()
    groupId.setAttribute('readonly', true)
    if (groupFormBtn[0].hasAttribute('disabled'))
        groupFormBtn[0].removeAttribute('disabled')
    if (groupFormBtn[2].hasAttribute('disabled'))
        groupFormBtn[2].removeAttribute('disabled')
    if (groupFormBtn[1].hasAttribute('disabled'))
        groupFormBtn[1].removeAttribute('disabled')
})

groupInput.addEventListener('input', () => {
    if (groupInput.value == null || groupInput.value == '')
        groupFormBtn[3].setAttribute('disabled', true)
    else groupFormBtn[3].removeAttribute('disabled')
})

let error = document.querySelectorAll('.error')
groupId.addEventListener('keyup', async e => {
    if (e.key == 'Enter') {
        let res = await fetch(`/group/${groupId.value.trim()}`)
        let result = res.json()
        result.then(data => {
            if ('error' in data[0]) {
                error[0].innerHTML = data[0].error
                setTimeout(() => {
                    error[0].innerHTML = ''
                }, 3000)
            } else {
                groupInput.value = data[0].group_name
                groupForm.setAttribute(
                    'action',
                    `http://localhost:8000/group/${groupId.value.trim()}`
                )
                method.innerHTML = ''
                let input = document.createElement('input')
                input.setAttribute('type', 'hidden')
                input.setAttribute('name', '_method')

                if (sessionStorage.getItem('update') == 'update') {
                    input.setAttribute('value', 'PUT')
                } else {
                    input.setAttribute('value', 'DELETE')
                }
                method.append(input)
            }
        })
    }
})

//Add Sub group form
let subGroupBtn = document.querySelectorAll('.subGroupBtn')
let subGroupForm = document.querySelector('#addSubGroupForm')
let subGroupId = document.getElementById('subGroupId')
let subGroupInput = document.getElementById('sub_group_name')
subGroupBtn[0].addEventListener('click', e => {
    e.preventDefault()
    window.location.reload()
})

subGroupBtn[1].addEventListener('click', e => {
    e.preventDefault()
    subGroupBtn[0].setAttribute('disabled', true)
    subGroupBtn[2].setAttribute('disabled', true)
    subGroupId.removeAttribute('readonly')
    if (sessionStorage.key(0) == 'sub-update')
        sessionStorage.removeItem('sub-update')
    sessionStorage.setItem('sub-delete', 'delete')
    subGroupId.value = ''
    subGroupId.focus()
})

subGroupBtn[2].addEventListener('click', e => {
    e.preventDefault()
    subGroupBtn[0].setAttribute('disabled', true)
    subGroupBtn[1].setAttribute('disabled', true)
    subGroupId.removeAttribute('readonly')
    if (sessionStorage.key(0) == 'sub-delete')
        sessionStorage.removeItem('sub-delete')
    sessionStorage.setItem('sub-update', 'update')
    subGroupId.value = ''
    subGroupId.focus()
})

subGroupBtn[4].addEventListener('click', e => {
    e.preventDefault()
    subGroupForm.reset()
    subGroupId.setAttribute('readonly', true)
    if (subGroupBtn[0].hasAttribute('disabled'))
        subGroupBtn[0].removeAttribute('disabled')
    if (subGroupBtn[2].hasAttribute('disabled'))
        subGroupBtn[2].removeAttribute('disabled')
    if (subGroupBtn[1].hasAttribute('disabled'))
        subGroupBtn[1].removeAttribute('disabled')
})

subGroupInput.addEventListener('input', () => {
    if (subGroupInput.value == null || subGroupInput.value == '')
        subGroupBtn[3].setAttribute('disabled', true)
    else subGroupBtn[3].removeAttribute('disabled')
})

let method1 = document.getElementById('method1')
subGroupId.addEventListener('keyup', async e => {
    if (e.key == 'Enter') {
        let res = await fetch(`/sub-group/show/${subGroupId.value.trim()}`)
        let result = res.json()
        result.then(data => {
            if ('error' in data[0]) {
                error[2].innerHTML = data[0].error
                setTimeout(() => {
                    error[2].innerHTML = ''
                }, 3000)
            } else {
                subGroupInput.value = data[0].sub_group_name
                let groups = document.querySelectorAll(
                    '#group_name_sub .group_option'
                )
                groups.forEach(ele => {
                    if (ele.value == data[0].group.group_id) {
                        ele.setAttribute('selected', true)
                    } else {
                        ele.removeAttribute('selected')
                    }
                })
                subGroupForm.setAttribute(
                    'action',
                    `http://localhost:8000/sub-group/${subGroupId.value.trim()}`
                )
                method1.innerHTML = ''
                let input = document.createElement('input')
                input.setAttribute('type', 'hidden')
                input.setAttribute('name', '_method')

                if (sessionStorage.getItem('sub-update') == 'update') {
                    input.setAttribute('value', 'PUT')
                } else {
                    input.setAttribute('value', 'DELETE')
                }
                method1.append(input)
            }
        })
    }
})
document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
