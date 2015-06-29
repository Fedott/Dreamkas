define(function(require) {
    //requirements

    return {
        //common
        '(/)': require('kit/rootRoute/rootRoute'),

        //dashboard
        'firstStart(/)': require('pages/firstStart/firstStart'),
        'dashboard(/)': require('pages/dashboard/dashboard'),

        'settings(/)': require('pages/settings/settings'),
        'logout(/)': require('kit/logout/logout'),

		//stockMovement
		'stockMovements(/)': require('pages/stockMovements/stockMovements'),

        //cashFlow
        'cashFlow(/)': require('pages/cashFlow/cashFlow'),

		//reports
		'reports(/)': require('pages/reports/reports'),
		'reports/stockBalance(/)': require('pages/reports/stockBalance/stockBalance'),
		'reports/profit/groups(/)': require('pages/reports/profit/groups/groups'),
		'reports/profit/groups/:groupId(/)': require('pages/reports/profit/groups/group/group'),
        'reports/profit/stores(/)': require('pages/reports/profit/stores/stores'),

        //catalog
        'catalog(/)': require('pages/catalog/catalog'),
        'catalog/groups/:groupId(/)': require('pages/catalog/groups/group/group'),

        //suppliers
        'suppliers(/)': require('pages/suppliers/suppliers'),

        //stores
        'stores(/)': require('pages/stores/stores'),

        //pos
        'pos(/)': require('pages/pos/pos'),
        'pos/stores/:storeId(/)': require('pages/pos/store/store'),
		'pos/stores/:storeId/sales(/)': require('pages/pos/sales/sales'),

        //404
        '*path': require('pages/404/404')
    };
});
