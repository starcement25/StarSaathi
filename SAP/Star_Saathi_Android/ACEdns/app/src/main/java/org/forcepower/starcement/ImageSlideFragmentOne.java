package org.forcepower.starcement;

import android.os.Bundle;
import androidx.fragment.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

/**
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 * A Fragment represents a behavior or a portion of user interface in an Activity
 */
// FragmentHome : Calling this child fragment which hold Slider Image

public final class ImageSlideFragmentOne extends Fragment
{

    public static ImageSlideFragmentOne newInstance(int pos, int drawable)
    {
        Bundle args = new Bundle();
        args.putInt("get_id", pos);
        args.putInt("get_image_link", drawable);

        ImageSlideFragmentOne fragment = new ImageSlideFragmentOne();
        fragment.setArguments(args);
        return fragment;
    }


    @Override
    public void onCreate(Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        super.onCreate(savedInstanceState);

    }

    @Override
    public View onCreateView(LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        View vi = inflater.inflate(R.layout.fragment_imgslide, container, false);

//       final ImageView ivHelpBack = (ImageView)vi.findViewById(R.id.ivHelpBack);
//       final ImageView ivHelpFor = (ImageView)vi.findViewById(R.id.ivHelpFor);
       final ImageView ivHelp = (ImageView)vi.findViewById(R.id.ivHelp);
        ivHelp.setBackgroundResource(get_image_link());

//        if(get_id() == 0)
//        {
//            ivHelpBack.setVisibility(View.INVISIBLE);
//        }
//        if(get_id() == intDrawable.length-1)
//        {
//            ivHelpFor.setVisibility(View.INVISIBLE);
//        }
        return vi;
    }

    public int get_image_link() {return getArguments().getInt("get_image_link");
    }
    public int get_id() {
        return getArguments().getInt("get_id");
    }
}

