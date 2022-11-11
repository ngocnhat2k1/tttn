function padTo2Digits(num) {
    return num.toString().padStart(2, '0');
}

export default function formatDate(date) {
    const fullDate = new Date(date)
    return (
        [
            fullDate.getFullYear(),
            padTo2Digits(fullDate.getMonth() + 1),
            padTo2Digits(fullDate.getDate()),
        ].join('-') +
        ' ' +
        [
            padTo2Digits(fullDate.getHours()),
            padTo2Digits(fullDate.getMinutes()),
            padTo2Digits(fullDate.getSeconds()),
        ].join(':')
    );
}
