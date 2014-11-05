//
//  KeyboardEventsListenerProtocol.h
//  dreamkas
//
//  Created by sig on 31.10.14.
//  Copyright (c) 2014 Dreamkas. All rights reserved.
//

#import <Foundation/Foundation.h>

@protocol KeyboardEventsListenerProtocol <NSObject>

@optional

/** Уведомление о показе клавиатуры */
- (void)keyboardWillAppear:(NSNotification *)notification;
- (void)keyboardDidAppear:(NSNotification *)notification;

/** Уведомление о скрытии клавиатуры */
- (void)keyboardWillDisappear:(NSNotification *)notification;
- (void)keyboardDidDisappear:(NSNotification *)notification;

@end
