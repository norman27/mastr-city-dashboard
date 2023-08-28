// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: myPieChartLabels,
    datasets: [{
      data: myPieChartValues,
      backgroundColor: ['#ff6961', '#ffb480', '#f8f38d', '#42d6a4', '#59adf6', '#9d94ff', '#c780e8'],
      hoverBackgroundColor: ['#8d0700', '#993f00', '#938c09', '#145c44', '#07477f', '#0e00a1', '#5a167a'],
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: true
    },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.labels[tooltipItem.index];
          return datasetLabel + ': ' + Math.round(chart.datasets[0].data[tooltipItem.index] * 10) / 10 + ' kWp';
        }
      }
    },
    cutoutPercentage: 80,
  },
});

var ctx2 = document.getElementById("typesOfUseChart");
var typesOfUseChart = new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: typesOfUseLabels,
    datasets: [{
      data: typesOfUseValues,
      backgroundColor: ['#ff6961', '#ffb480', '#f8f38d', '#42d6a4', '#59adf6', '#9d94ff', '#c780e8'],
      hoverBackgroundColor: ['#8d0700', '#993f00', '#938c09', '#145c44', '#07477f', '#0e00a1', '#5a167a'],
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: true
    },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.labels[tooltipItem.index];
          return datasetLabel + ': ' + Math.round(chart.datasets[0].data[tooltipItem.index] * 10) / 10 + ' kWp';
        }
      }
    },
    cutoutPercentage: 80,
  },
});
