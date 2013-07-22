package project.lighthouse.autotests.objects;

import org.json.JSONException;
import org.json.JSONObject;

public class Group {

    static public String DEFAULT_NAME = "defaultGroup";

    JSONObject jsonObject;

    public Group(JSONObject jsonObject) {
        this.jsonObject = jsonObject;
    }

    public String getId() throws JSONException {
        return jsonObject.getString("id");
    }

    public String getGroupName() throws JSONException {
        return jsonObject.getString("name");
    }

    public static JSONObject getJsonObject(String name) throws JSONException {
        return new JSONObject()
                .put("name", name);
    }
}
