package org.forcepower.starcement;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentStatePagerAdapter;

import java.util.List;

/**
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 * Adapter is described in the API documentation, as "a bridge between an AdapterView and the underlying data for that view".
 */


public final class PageAdapterFirstSlide extends FragmentStatePagerAdapter {

    private List<Fragment> fragmentLists;

    public PageAdapterFirstSlide(final FragmentManager manager, final List<Fragment> fragmentLists) {
        super(manager);
        this.fragmentLists = fragmentLists;
    }

    @Override
    public Fragment getItem(int position) {
        // TODO Auto-generated method stub
        return fragmentLists.get(position);
    }

    @Override
    public int getCount() {
        // TODO Auto-generated method stub
        return fragmentLists.size();
    }
}