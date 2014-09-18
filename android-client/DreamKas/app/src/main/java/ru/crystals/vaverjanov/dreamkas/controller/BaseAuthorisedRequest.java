package ru.crystals.vaverjanov.dreamkas.controller;

import com.octo.android.robospice.request.SpiceRequest;

import org.androidannotations.annotations.Bean;
import org.androidannotations.annotations.EBean;
import org.androidannotations.annotations.rest.RestService;

import java.lang.reflect.ParameterizedType;

import ru.crystals.vaverjanov.dreamkas.model.NamedObject;
import ru.crystals.vaverjanov.dreamkas.model.NamedObjects;
import ru.crystals.vaverjanov.dreamkas.model.Token;

@EBean
public abstract class BaseAuthorisedRequest extends SpiceRequest<NamedObjects>
{
    @RestService
    protected LighthouseRestClient restClient;
    protected String token;

    public BaseAuthorisedRequest()
    {
        super(NamedObjects.class);
    }

    public void setToken(String token)
    {
        this.token = token;
        restClient.setHeader("Authorization", "Bearer " + token);
    }
}
