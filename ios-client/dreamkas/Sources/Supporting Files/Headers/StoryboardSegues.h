//
//  StoryboardSegues.h
//  dreamkas
//
//  Created by sig on 09.10.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#ifndef dreamkas_StoryboardSegues_h
#define dreamkas_StoryboardSegues_h

//
// Идентификаторы переходов между экранами
//

#define IntroToAuthScreenSegueName                  @"IntroToAuthScreenSegue"
#define IntroToCashierPwdScreenSegueName            @"IntroToCashierPwdScreenSegue"

#define AuthToTicketWindowScreenSegueName           @"AuthToTicketWindowScreenSegue"
#define AuthToLogInScreenSegueName                  @"AuthToLogInScreenSegue"
#define AuthToSignInScreenSegueName                 @"AuthToSignInScreenSegue"

#define LogInToTicketWindowSegueName                @"LogInToTicketWindowSegue"
#define SignInToTicketWindowSegueName               @"SignInToTicketWindowSegue"

#define TicketWindowToSelectStoreSegueName          @"TicketWindowToSelectStoreSegue"
#define TicketWindowToPaymentScreenSegueName        @"TicketWindowToPaymentScreenSegue"

#define GroupsToProductsSegueName                   @"GroupsToProductsSegue"
#define GroupsToSearchSegueName                     @"GroupsToSearchSegue"

//
// Идентификаторы экранов
//

#define MainNavigationControllerID                  @"MainNavigationControllerID"
#define LogInViewControllerID                       @"LogInViewControllerID"
#define SignInViewControllerID                      @"SignInViewControllerID"

#define SelectStoreViewControllerID                 @"SelectStoreViewControllerID"
#define SidemenuViewControllerID                    @"SidemenuViewControllerID"

#define LeftSideNavigationControllerID              @"LeftSideNavigationControllerID"
#define RightSideNavigationControllerID             @"RightSideNavigationControllerID"

#define GroupsViewControllerID                      @"GroupsViewControllerID"
#define SearchViewControllerID                      @"SearchViewControllerID"
#define SaleViewControllerID                        @"SaleViewControllerID"

#define PaymentViewControllerID                     @"PaymentViewControllerID"
#define FinalPaymentViewControllerID                @"FinalPaymentViewControllerID"

#endif
