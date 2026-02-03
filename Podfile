platform :ios, '13.0'

source 'https://cdn.cocoapods.org/'

target 'StarCementDealer' do
  use_frameworks!
  use_modular_headers!

  # Networking
  # pod 'AFNetworking', '~> 4.0'   # ✅ Updated: AFNetworking 4+ has NO UIWebView
  pod 'Alamofire', '~> 5.6'

  # UI / Utilities
  pod 'SVProgressHUD'
  pod 'ICViewPager'
  pod 'KeychainAccess', '~> 4.2'

  # Firebase (optional, if you enable)
  # pod 'Firebase/Core'
  # pod 'Firebase/Messaging'
end

post_install do |installer|
  installer.pods_project.targets.each do |target|
    target.build_phases.each do |phase|
      # Force shell script build phases to use bash (fixes "unbound variable" errors)
      if phase.respond_to?(:shell_path)
        phase.shell_path = '/bin/bash'
      end
    end
  end
end