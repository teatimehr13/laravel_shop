import dayjs from 'dayjs';

function toCurrency(num) {
    if (!num && num !== 0) return "$"; // 避免 null 或 undefined
    return `$${Number(num).toLocaleString("en-US")}`; // 確保是數字再轉換
}

function formateDate(rawTime, format = 'YYYY/MM/DD HH:mm') {
    const formatted = dayjs(rawTime).format(format)
    return formatted;
}

export { toCurrency, formateDate }