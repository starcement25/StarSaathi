package org.forcepower.starcement;

import java.io.Serializable;

/**
 * Created by Amitabha
 */
public final class CategoryItem implements Serializable
{
    private String name, small, timestamp, cat_id;
    boolean Selected = false;
    public CategoryItem() {}

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public boolean getSelected() {
        return Selected;
    }

    public void setSelected(boolean Selected) {
        this.Selected = Selected;
    }
}
