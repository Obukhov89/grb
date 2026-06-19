let projectTable = {}
let secondmentTable = {}

$.ajax({
    url: `/ulab/project/getDashboardData`,
    type: "POST", //метод отправки
    dataType: 'json', // data type
    data: { project_id: 1 },
    success: function (data) {
        console.log(data)
       // secondmentTable = getSecondmentTable(data?.secondment)
      //  projectTable = getProjectTable(data?.project)

        $("#project_name").text(data[0]?.name)

    },
    error: function (xhr, resp, text) {
        console.log(xhr, resp, text);
    }
})
