package ru.dreamkas.pos.view.components;

import android.content.Context;
import android.content.res.TypedArray;
import android.text.InputFilter;
import android.text.InputType;
import android.text.Spanned;
import android.util.AttributeSet;
import android.widget.EditText;

import org.apache.commons.lang3.StringUtils;

import java.math.BigDecimal;
import java.text.DecimalFormat;
import java.text.DecimalFormatSymbols;
import java.text.ParseException;

import ru.dreamkas.pos.Constants;
import ru.dreamkas.pos.DreamkasApp;
import ru.dreamkas.pos.R;

public class NumericEditText extends EditText{
    private DecimalFormat mDecimalFormat;
    private Integer mMaximumFractionDigits = 1;
    private Integer mMinimumFractionDigits = 1;

    private InputFilter mInputFilter = new InputFilter() {
        public CharSequence filter(CharSequence source, int start, int end, Spanned dest, int dstart, int dend) {
            String currentValue = getText().toString().substring(0, dstart);
            String separator = String.valueOf(mDecimalFormat.getDecimalFormatSymbols().getDecimalSeparator());

            StringBuilder sb = new StringBuilder(source);

            for (int i = 0; i < sb.length(); i++) {
                char symbol = sb.charAt(i);
                Boolean filterIt = filterSymbol(symbol, currentValue, separator, Constants.DOT, Constants.COMMA);
                if(filterIt){
                    sb.deleteCharAt(i);
                    i--;
                }
            }

            if(sb.equals(source)){
                return null;
            }else {
                return sb;
            }

        }
    };

    private Boolean filterSymbol(char symbol,String currentValue, String separator, char dot, char comma) {
        if((symbol == dot || symbol == comma) && (currentValue.contains(separator) || currentValue.contains(separator))){
            return true;
        }

        String[] valueParts = currentValue.split(separator);

        if(valueParts.length > 1){
            String lastBlock = valueParts[valueParts.length - 1];
            if(lastBlock.length() == mMaximumFractionDigits){
                return true;
            }
        }

        return false;
    }

    public NumericEditText(Context context) {
        super(context);
        init();
    }

    public NumericEditText(Context context, AttributeSet attrs) {
        super(context, attrs);
        setAttributes(context, attrs);
        init();
    }

    public NumericEditText(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        setAttributes(context, attrs);
        init();
    }

    private void setAttributes(Context context, AttributeSet attrs) {
        TypedArray a = context.obtainStyledAttributes(attrs, R.styleable.numeric_edit_text);
        int minDigits = a.getInteger(R.styleable.numeric_edit_text_minimum_fraction_digits, -1);
        int maxDigits = a.getInteger(R.styleable.numeric_edit_text_maximum_fraction_digits, -1);
        if (minDigits != -1) {
            mMinimumFractionDigits = minDigits;
        }
        if (maxDigits != -1) {
            mMaximumFractionDigits= maxDigits;
        }
        a.recycle();
    }

    private void init(){
        //setRawInputType(InputType.TYPE_NUMBER_FLAG_DECIMAL);

        setRawInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        mMaximumFractionDigits = 3;
        changeFormat(Constants.COMMA);
        setFilters(new InputFilter[]{mInputFilter});
    }

    public BigDecimal getValue(){
        try
        {
            String str = getText().toString();

            char newSeparator = getCurrentSeparator(str);
            if(mDecimalFormat.getDecimalFormatSymbols().getDecimalSeparator() != newSeparator){
                changeFormat(newSeparator);
            }

            BigDecimal bd = (BigDecimal)mDecimalFormat.parse(str);
            return bd.setScale(mMaximumFractionDigits, BigDecimal.ROUND_HALF_UP);
        }catch (ParseException ex){
            setInputType(InputType.TYPE_TEXT_FLAG_NO_SUGGESTIONS);
            setError(DreamkasApp.getResourceString(R.string.msg_error_wrong_format));
        }
        return null;
    }

    public void setValue(BigDecimal value) {
        setText(mDecimalFormat.format(value));
    }

    private char getCurrentSeparator(String str) {
        int dotsCount = StringUtils.countMatches(str, String.valueOf(Constants.DOT));
        int commasCount = StringUtils.countMatches(str, String.valueOf(Constants.COMMA));

        if(dotsCount > 0 && commasCount > 0){
            setError(DreamkasApp.getResourceString(R.string.msg_error_wrong_format));
            return Constants.COMMA;
        }else if(dotsCount > 0){
            if(dotsCount > 1){
                setError(DreamkasApp.getResourceString(R.string.msg_error_wrong_format));
            }
            return Constants.DOT;
        }else {
            if(commasCount > 1){
                setError(DreamkasApp.getResourceString(R.string.msg_error_wrong_format));
            }
            return Constants.COMMA;
        }
    }

    private void changeFormat(char separator) {
        DecimalFormatSymbols otherSymbols = new DecimalFormatSymbols();
        otherSymbols.setDecimalSeparator(separator);

        mDecimalFormat = new DecimalFormat("", otherSymbols);
        mDecimalFormat.setParseBigDecimal(true);
        mDecimalFormat.setMinimumFractionDigits(mMinimumFractionDigits);
        mDecimalFormat.setMaximumFractionDigits(mMaximumFractionDigits);
    }


}
