package project.lighthouse.autotests.objects;

import org.json.JSONException;
import org.json.JSONObject;

public class Category {

    JSONObject jsonObject;

    public Category(JSONObject jsonObject) {
        this.jsonObject = jsonObject;
    }

    public String getId() throws JSONException {
        return jsonObject.getString("id");
    }

    public static JSONObject getJsonObject(String name, String groupId) throws JSONException {
        return new JSONObject()
                .put("name", name)
                .put("group", groupId);
    }
}
