package org.forcepower.starcement.adapter;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.viewpager2.adapter.FragmentStateAdapter;

import java.util.ArrayList;

public final class ViewPagerFragmentAdapter extends FragmentStateAdapter
{
    private ArrayList<Fragment> fragments;

    public ViewPagerFragmentAdapter(@NonNull final FragmentActivity fragment, final ArrayList<Fragment> fragments) {
        super(fragment);
        this.fragments = fragments;
    }

    @NonNull
    @Override
    public Fragment createFragment(final int position) {
        return fragments.get(position);
    }

    @Override
    public int getItemCount() {
        return fragments.size();
    }
}