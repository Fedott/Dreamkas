//
//  IntroViewController.m
//  dreamkas
//
//  Created by sig on 09.10.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#import "IntroViewController.h"
#import "CustomLabel.h"

static const CGFloat TimeoutBeforeStart = 2.0f;

@interface IntroViewController()

@property (nonatomic, weak) IBOutlet CustomLabel *titleLabel;

@end

@implementation IntroViewController

#pragma mark - View Lifecicle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    [self.titleLabel setText:NSLocalizedString(@"intro_screen_title", nil)];
    
    // запускаем логику по таймауту
    [NSTimer scheduledTimerWithTimeInterval:TimeoutBeforeStart target:self
                                   selector:@selector(startLogic) userInfo:nil repeats:NO];
}

#pragma mark - Основная логика работы

/**
 *  Переход к основному экрану приложения
 */
- (void)startLogic
{
    if ([CurrentUser hasActualAuthData]) {
        [NetworkManager authWithLogin:[CurrentUser lastUsedLogin]
                             password:[CurrentUser lastUsedPassword]
                         onCompletion:^(NSDictionary *data, NSError *error)
         {
             if (error == nil) {
                 // если авторизация прошла успешно - переходим к кассе
                 [self performSegueWithIdentifier:IntroToCashierPwdScreenSegueName sender:self];
             }
             else {
                 // TODO: show error message
             }
         }];
    }
    else {
        [self performSegueWithIdentifier:IntroToAuthScreenSegueName sender:self];
    }
}

@end
