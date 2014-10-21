//
//  TicketWindowViewController.m
//  dreamkas
//
//  Created by sig on 09.10.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#import "TicketWindowViewController.h"

@interface TicketWindowViewController ()

@property (nonatomic, weak) IBOutlet UITableView *tableView;

@end

@implementation TicketWindowViewController

#pragma mark - View Lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    // ..
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    // ..
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    if ([[CurrentUser lastUsedStoreID] length] < 1) {
        [self showViewControllerModally:ControllerById(SelectStoreViewControllerID)
                                segueId:TicketWindowToSelectStoreSegueName];
    }
}

#pragma mark - Configuration Methods

- (void)configureLocalization
{
    // ..
}

- (void)configureAccessibilityLabels
{
    [self.tableView setAccessibilityLabel:AI_TicketWindowPage_Table];
}

#pragma mark - Обработка пользовательского взаимодействия

- (IBAction)sidemenuButtonClicked:(id)sender
{
    DPLogFast(@"");
    
    [self showSidemenu:^{
        // ..
    }];
}

@end
