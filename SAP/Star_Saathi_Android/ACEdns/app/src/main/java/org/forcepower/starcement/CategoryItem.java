package org.forcepower.starcement;

import java.io.Serializable;

/**
 * Created by Amitabha
 */
public final class CategoryItem implements Serializable
{
    private String name, small, timestamp, cat_id;
    boolean Selected = false;
    public CategoryItem()
    {
        //
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getImageUrl() {
        return small;
    }

    public void setImageUrl(String small) {
        this.small = small;
    }

    public String getDescription() {
        return timestamp;
    }

    public void setDescription(String timestamp) {
        this.timestamp = timestamp;
    }

    public String getCategoryId() {
        return cat_id;
    }

    public void setCategoryId(String cat_id) {
        this.cat_id = cat_id;
    }

    public boolean getSelected() {
        return Selected;
    }

    public void setSelected(boolean Selected) {
        this.Selected = Selected;
    }
}
