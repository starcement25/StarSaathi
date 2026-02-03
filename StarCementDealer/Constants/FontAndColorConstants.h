//
//  FontAndColorConstants.h
//  EO-GOALS
//
//  Created by Coral  on 19/05/16.
//  Copyright © 2016 Coral . All rights reserved.
//






static NSString * const PFDinTextPro_Regular            = @"PFDinTextPro-Regular";
static NSString * const PFDinTextPro_Thin               = @"PFDinTextPro-Thin";
static NSString * const PFDinTextPro_ExtraBlackItalic   = @"PFDinTextPro-ExtraBlackItalic";
static NSString * const PFDinTextPro_ExtraThinItalic    = @"PFDinTextPro-ExtraThinItalic";
static NSString * const PFDinTextPro_ExtraThin          = @"PFDinTextPro-ExtraThin";
static NSString * const PFDinTextPro_LightItalic        = @"PFDinTextPro-LightItalic";
static NSString * const PFDinTextPro_MediumItalic       = @"PFDinTextPro-MediumItalic";
static NSString * const PFDinTextPro_Bold               = @"PFDinTextPro-Bold";
static NSString * const PFDinTextPro_ThinItalic         = @"PFDinTextPro-ThinItalic";
static NSString * const PFDinTextPro_Medium             = @"PFDinTextPro-Medium";
static NSString * const PFDinTextPro_ExtraBlack         = @"PFDinTextPro-ExtraBlack";
static NSString * const PFDinTextPro_Light              = @"PFDinTextPro-Light";
static NSString * const PFDinTextPro_Italic             = @"PFDinTextPro-Italic";
static NSString * const PFDinTextPro_BoldItalic         = @"PFDinTextPro-BoldItalic";




NS_INLINE void FetchAllFont()
{ for(NSString *strFamily in [UIFont familyNames]) { for (NSString *strFont in [UIFont fontNamesForFamilyName:strFamily])
{  NSLog(@"Family::%@ \t font ::%@", strFamily, strFont);     }}}

NS_INLINE UIFont* Font_Regular(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_Regular size:fltSize]; }

NS_INLINE UIFont* Font_Thin(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_Thin size:fltSize]; }

NS_INLINE UIFont* Font_ExtraBlackItalic(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_ExtraBlackItalic size:fltSize]; }

NS_INLINE UIFont* Font_ExtraThinItalic(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_ExtraThinItalic size:fltSize]; }

NS_INLINE UIFont* Font_ExtraThin(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_ExtraThin size:fltSize]; }

NS_INLINE UIFont* Font_LightItalic(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_LightItalic size:fltSize]; }

NS_INLINE UIFont* Font_MediumItalic(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_MediumItalic size:fltSize]; }

NS_INLINE UIFont* Font_Bold(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_Bold size:fltSize]; }

NS_INLINE UIFont* Font_ThinItalic(float fltSize)
{ return [UIFont fontWithName:PFDinTextPro_ThinItalic size:fltSize]; }

NS_INLINE UIFont* Font_Medium(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_Medium size:fltSize]; }

NS_INLINE UIFont* Font_ExtraBlack(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_ExtraBlack size:fltSize]; }

NS_INLINE UIFont* Font_Light(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_Light size:fltSize]; }

NS_INLINE UIFont* Font_Italic(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_Italic size:fltSize]; }

NS_INLINE UIFont* Font_BoldItalic(float fltSize)
{  return [UIFont fontWithName:PFDinTextPro_BoldItalic size:fltSize]; }




NS_INLINE UIColor *Color_RGB(float red ,float green ,float blue)
{ return [UIColor colorWithRed:red/255.0 green:green/255.0 blue:blue/255.0 alpha:1.0]; }

//#define Color_Yellow        [UIColor colorWithRed:245/255.0 green:174/255.0 blue:48/255.0 alpha:1.0]
//#define Color_Yellow_2      [UIColor colorWithRed:185/255.0 green:139/255.0 blue:107/255.0 alpha:1.0]
