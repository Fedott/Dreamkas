define({
    'recalc_product_price': 'Перерасчет цен продукта',
    'set10_export_products': 'Выгрузка товаров в Set Retail 10',

    'invalid_grant': 'Неверный логин или пароль',
    'invalid_request': 'Пожалуйста заполните обязательные поля логин и пароль',
    'Group is not empty': 'Группа не пуста. Сначала удалите из нее все категории.',
    'Category is not empty': 'Категория не пуста. Сначала удалите из нее все подкатегории.',
    'SubCategory is not empty': 'Подкатегория не пуста. Сначала удалите из нее все товары.',

    'pending': 'в очереди',
    'success': 'выполнено',

    nearest50: 'до 50 копеек',
    nearest1: 'до копеек',
    nearest10: 'до 10 копеек',
    nearest100: 'до рублей',
    nearest99: 'до 99 копеек',

    productTypes: function(type, format) {

        format = format || 'nameCapitalFull';

        var productTypes = {
            unit: {
                units: 'unit',
                nameCapitalFull: 'Штучный',
                nameSmallFull: 'штучный'
            },
            weight: {
                units: 'kg',
                nameCapitalFull: 'Весовой',
                nameSmallFull: 'весовой'
            },
            alcohol: {
                units: 'unit',
                nameCapitalFull: 'Алкоголь',
                nameSmallFull: 'алкоголь'
            }
        };

        return type ? productTypes[type][format] : '';
    },

    units: function(unit, format) {

        format = format || 'smallShort';

        var unitsEnums = {
            kg: {
                capitalFull: "Килограммы",
                smallFull: "килограмм",
                smallShort: "кг"
            },
            unit: {
                capitalFull: "Штуки",
                smallFull: "штука",
                smallShort: "шт."
            },
            liter: {
                capitalFull: "Литры",
                smallFull: "литр",
                smallShort: "л"
            }
        };

        return unit ? unitsEnums[unit][format] : '';
    },
    'for last week': function(day) {
        switch (day) {
            case 1:
                return 'прошлый вторник';
            case 2:
                return 'прошлую среду';
            case 3:
                return 'прошлый четверг';
            case 4:
                return 'прошлую пятницу';
            case 5:
                return 'прошлую субботу';
            case 6:
                return 'прошлое воскресение';
            default:
                return 'прошлый понедельник';
        }
    },
    'for last day': function(day) {
        switch (day) {
            case 1:
                return 'прошлый понедельник';
            case 2:
                return 'прошлый вторник';
            case 3:
                return 'прошлую среду';
            case 4:
                return 'прошлый четверг';
            case 5:
                return 'прошлую пятницу';
            case 6:
                return 'прошлую субботу';
            default:
                return 'прошлое воскресение';
        }
    },
    'last day': function(day) {
        switch (day) {
            case 1:
                return 'прошлый понедельник';
            case 2:
                return 'прошлый вторник';
            case 3:
                return 'прошлая среда';
            case 4:
                return 'прошлый четверг';
            case 5:
                return 'прошлая пятница';
            case 6:
                return 'прошлая суббота';
            default:
                return 'прошлое воскресение';
        }
    }
});