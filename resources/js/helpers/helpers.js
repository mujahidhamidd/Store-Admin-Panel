

export function saveUserToLocalStorage(userInfo) {
    localStorage.setItem('userInfo', JSON.stringify(userInfo))
}



export function setAutorizationHeaders(token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`
}

export function getLoggedInUser() {
    let userInfo = localStorage.getItem("userInfo");
    if (!userInfo) {
        return null
    }
    return JSON.parse(userInfo)
}
