// Locale
var locale = {
	charset: "utf-8",
    commentList: {
        mesHtml: "Функция добавления комментария возможна только для авторизованных пользователей.\n<a href='/users/?from=true'>Авторизуйтесь или пройдите регистрацию</a>.",
        mesSimple: "Функция добавления комментария возможна только для авторизованных пользователей.\nАвторизуйтесь или пройдите регистрацию.",
        mes: "Ваш комментарий будет доступен другим пользователям только после прохождения модерации...",
        dell: "Вы действительно хотите удалить комментарий?",
    },
    OrderChekJq: {
        badReqEmail: "Пожалуйста, укажите корректный E-mail",
        badReqName: "Обратите внимание,\nимя должно состоять не менее чем из 3 букв",
        badReq: "Обратите внимание,\nесть поля, обязательные для заполнения",
        badDelivery: "Пожалуйста,\nвыберите доставку"
    },
    commentAuthErrMess: "Добавить комментарий может только авторизованный пользователь.\n<a href='" + ROOT_PATH + "/users/?from=true'>Пожалуйста, авторизуйтесь или пройдите регистрацию</a>.",
    incart: "В корзине",
    cookie_message: "С целью предоставления наиболее оперативного обслуживания на данном сайте используются cookie-файлы. Используя данный сайт, вы даете свое согласие на использование нами cookie-файлов.",
	show: "Показать",
	hide: "Скрыть",
	close: "Закрыть",
	FlipClock: {
        years:"лет",
        months:"месяцев",
        days:"дней",
        hours:"часов",
        minutes:"минут",
        seconds:"секунд"
    },
	cdek: {
        express_delivery: "Курьерская доставка",
		pickup_code: "Код выбранного ПВЗ",
		city: "город",
		pickup_address: "адрес выбранного ПВЗ",
		pickup_phone: "телефон выбранного ПВЗ",
		pickup: "ПВЗ",
	    country: "Россия"
	},
	pochta: {
        pickup: "Почта России, отделение"
    }
    
};

$().ready(function () {
    locale_def = locale;
});