// /** @format */

// // /** @format */

// jQuery(document).ready(function($) {
//   function GethashID(hashIDName) {
//     if (hashIDName) {
//       $(".tab li")
//         .find("a")
//         .each(function() {
//           var idName = $(this).attr("href");
//           if (idName == hashIDName) {
//             var parentElm = $(this).parent();
//             $(".tab li").removeClass("active");
//             $(parentElm).addClass("active");
//             $(".area").removeClass("is-active");
//             $(hashIDName).addClass("is-active");
//           }
//         });
//     }
//   }

//   $(".tab a").on("click", function() {
//     var idName = $(this).attr("href");
//     GethashID(idName);
//     history.pushState(null, null, idName); // URLにハッシュを追加
//     return false;
//   });

//   // URLにハッシュが存在する場合のみタブの初期化を実行
//   if (window.location.hash) {
//     var hashName = window.location.hash; // URLからハッシュタグを取得
//     GethashID(hashName); // タブとエリアのアクティブ化
//   } else {
//     // ハッシュがない場合、デフォルトのタブを設定
//     $(".tab li:first").addClass("active");
//     $(".area:first").addClass("is-active");
//   }

//   // ブラウザの戻る/進むボタンが押された時にタブを切り替える
//   $(window).on("popstate", function() {
//     var hashName = window.location.hash;
//     if (hashName) {
//       GethashID(hashName);
//     } else {
//       // ハッシュがない場合、デフォルトのタブを設定
//       $(".tab li:first").addClass("active");
//       $(".area:first").addClass("is-active");
//     }
//   });
// });